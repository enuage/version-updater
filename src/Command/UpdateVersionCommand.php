<?php
/**
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code. Or visit
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace Enuage\VersionUpdaterBundle\Command;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\DependencyInjection\Configuration;
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
use Enuage\VersionUpdaterBundle\Exception\InvalidFileException;
use Enuage\VersionUpdaterBundle\Exception\VersionFinderException;
use Enuage\VersionUpdaterBundle\Finder\FilesFinder;
use Enuage\VersionUpdaterBundle\Finder\VersionFinder;
use Enuage\VersionUpdaterBundle\Formatter\FileFormatter;
use Enuage\VersionUpdaterBundle\Handler\AbstractHandler;
use Enuage\VersionUpdaterBundle\Handler\ComposerHandler;
use Enuage\VersionUpdaterBundle\Handler\JsonHandler;
use Enuage\VersionUpdaterBundle\Handler\StructureHandler;
use Enuage\VersionUpdaterBundle\Handler\TextHandler;
use Enuage\VersionUpdaterBundle\Handler\YamlHandler;
use Enuage\VersionUpdaterBundle\Helper\Type\FileType;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Parser\AbstractParser;
use Enuage\VersionUpdaterBundle\Parser\CommandOptionsParser;
use Enuage\VersionUpdaterBundle\Parser\ConfigurationParser;
use Enuage\VersionUpdaterBundle\Parser\FileParser;
use Enuage\VersionUpdaterBundle\Parser\VersionParser;
use Enuage\VersionUpdaterBundle\Service\VersionService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class UpdateVersionCommand
 *
 * @package Enuage\VersionUpdaterBundle\Command
 */
class UpdateVersionCommand extends ContainerAwareCommand
{
    public const COMMAND_NAME = 'enuage:version:update';

    private const HANDLERS = [
        'files' => TextHandler::class,
        FileType::TYPE_JSON => JsonHandler::class,
        FileType::TYPE_YAML => YamlHandler::class,
    ];

    /**
     * @var VersionOptions
     */
    private $options;

    /**
     * @var ArrayCollection
     */
    private $configurations;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var boolean
     */
    private $colors = true;

    /**
     * @var VersionService
     */
    private $service;

    /**
     * @var string
     */
    private $version;

    /**
     * UpdateVersionCommand constructor.
     *
     * @param ConfigurationParser $configurations
     */
    public function __construct(ConfigurationParser $configurations = null)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->configurations = $configurations ?: new ConfigurationParser();
        $this->service = new VersionService();
    }

    /**
     * @param array $configurations
     *
     * @return UpdateVersionCommand
     */
    public function setConfigurations(array $configurations): UpdateVersionCommand
    {
        $this->configurations = new ArrayCollection($configurations);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Update the version in project files');
        $this->addArgument('version', InputArgument::OPTIONAL, 'New version tag');
        $this->addOption('major', null, InputOption::VALUE_NONE, 'Update major version');
        $this->addOption('minor', null, InputOption::VALUE_NONE, 'Update minor version');
        $this->addOption('patch', null, InputOption::VALUE_NONE, 'Update patch version');

        $this->addOption(
            'down',
            null,
            InputOption::VALUE_NONE,
            'Decrease version. It\'s also applicable to prerelease versions'
        );

        // Prerelease versions
        $this->addOption('alpha', null, InputOption::VALUE_NONE, 'Increase or define the alpha version');
        $this->addOption('beta', null, InputOption::VALUE_NONE, 'Increase or define the beta version');
        $this->addOption('rc', null, InputOption::VALUE_NONE, 'Increase or define the release candidate version');
        $this->addOption('release', null, InputOption::VALUE_NONE, 'Remove all prerelease versions');

        // Metadata
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Add date metadata to the version');
        $this->addOption('meta', null, InputOption::VALUE_OPTIONAL, 'Add metadata to the version');

        $this->addOption(
            'config-file',
            null,
            InputOption::VALUE_OPTIONAL,
            'Path to configuration file or to the directory with ".enuage" file. Please check documentation: https://gitlab.com/enuage/bundles/version-updater/wikis/Configuration'
        );

        $this->addOption('colors', null, InputOption::VALUE_OPTIONAL, 'Enable/disable output colors.', true);
        $this->addOption('show-current', null, InputOption::VALUE_OPTIONAL, 'Show current version from source.');
        $this->addOption('exclude-git', null, InputOption::VALUE_NONE, 'Disable updating Git repository.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $this->options = CommandOptionsParser::parse($input);
        if ($this->configurations->isGitEnabled()) {
            $this->options->setGitVersion($this->service->getVersionFromGit());
        }

        $this->colors = filter_var($input->getOption('colors'), FILTER_VALIDATE_BOOLEAN);

        if ($showSource = $input->getOption('show-current')) {
            $this->showCurrentVersion($showSource);
        }

        if ($io->isVerbose()) {
            $debugMessage = 'Debug mode enabled.';
            $this->colors
                ? $this->io->block($debugMessage, 'DEBUG', 'fg=black;bg=yellow', ' ', true)
                : $this->io->writeln($debugMessage);
            $configurationsMessage = 'Configurations:';
            $this->colors ? $io->title($configurationsMessage) : $io->writeln($configurationsMessage);
            $this->options->consoleDebug($io);
        }

        $processStartMessage = 'Started files updating';
        $this->colors ? $io->title($processStartMessage) : $io->writeln($processStartMessage);

        try {
            if ($this->getContainer()->hasParameter(Configuration::CONFIG_ROOT)) {
                /** @noinspection NestedPositiveIfStatementsInspection */
                if ($configuration = $this->getContainer()->getParameter(Configuration::CONFIG_ROOT)) {
                    $this->configurations = ConfigurationParser::parseConfiguration($configuration);
                }
            }

            $finder = new FilesFinder();
            $configFile = $input->getOption('config-file');
            if (null !== $configFile) {
                $this->configurations = ConfigurationParser::parseFile($configFile, $finder);
            }

            foreach (self::HANDLERS as $type => $handler) {
                if ($files = $this->configurations->getFiles($type)) {
                    $finder->setFiles($files);

                    $handler = new $handler();
                    if ($handler instanceof StructureHandler) {
                        $finder->setExtensions($handler::getExtensions());
                    }

                    $this->updateFiles($finder, $handler);
                }
            }

            if ($this->configurations->isGitEnabled() && false === $input->getOption('exclude-git')) {
                $this->updateGit();
            }
        } catch (Exception $exception) {
            $this->colors ? $io->error($exception->getMessage()) : $io->writeln($exception->getMessage());
            exit(2);
        }

        $successMessage = 'All targets were successfully updated.';
        $this->colors ? $io->success($successMessage) : $io->writeln($successMessage);
        $this->io->writeln($this->version);
    }

    /**
     * @param FilesFinder $finder
     * @param AbstractHandler $handler
     *
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    private function updateFiles(FilesFinder $finder, AbstractHandler $handler): void
    {
        if ($finder->hasFiles()) {
            $finder->iterate(
                function ($file, $pattern) use ($handler) {
                    $handler->setPattern($pattern);

                    /** @var SplFileInfo $file */
                    if (ComposerHandler::FILENAME === $file->getBasename()) {
                        $handler = new ComposerHandler();
                    }

                    $fileParser = new FileParser($file, $handler);

                    $providedVersion = $this->options->getVersion();
                    if ($this->configurations->isGitEnabled()) {
                        $providedVersion = $this->options->getGitVersion();
                    }
                    $versionParser = new VersionParser($providedVersion);

                    /** @var AbstractParser $parser */
                    $parser = $this->options->hasVersion() ? $versionParser : $fileParser;

                    $mutator = new VersionMutator($parser->parse(), $this->options);

                    if (!$this->options->hasVersion()) {
                        $mutator->update();
                    }

                    $fileFormatter = new FileFormatter($fileParser);
                    $fileFormatter->setHandler($handler);
                    $this->version = $fileFormatter->format($mutator->getFormatter());

                    $updatedMessage = sprintf('Updated file "%s". Version: %s', $file, $this->version);
                    $this->colors ? $this->io->writeln('✔ '.$updatedMessage) : $this->io->writeln($updatedMessage);
                }
            );
        }
    }

    /**
     * @param $showSource
     */
    private function showCurrentVersion($showSource): void
    {
        $finder = new VersionFinder();
        $exitCode = 1;

        try {
            switch ($showSource) {
                case VersionFinder::SOURCE_COMPOSER:
                    $version = $finder->getComposerVersion();
                    break;
                case VersionFinder::SOURCE_GIT:
                    $version = $finder->getGitVersion();
                    break;
                default:
                    $version = null;
                    break;
            }

            if (true === filter_var($showSource, FILTER_VALIDATE_BOOLEAN)) {
                $finder->findAll()->cliOutput($this->io);
            }

            if (null !== $version) {
                $this->io->writeln($version);
            }
        } catch (Exception $exception) {
            $this->colors ? $this->io->error($exception->getMessage()) : $this->io->writeln($exception->getMessage());
            $exitCode = 2;
        }

        exit($exitCode);
    }

    /**
     * @throws VersionFinderException
     */
    private function updateGit(): void
    {
        $this->io->newLine();

        $gitUpdatingMessage = 'Updating Git repository';
        $this->colors ? $this->io->title($gitUpdatingMessage) : $this->io->writeln($gitUpdatingMessage);

        // TODO GitCommand::commit('.', 'Version update: $version');
        // TODO GitCommand::push();

        $commitMessage = 'All updated files were committed.';
        $this->colors ? $this->io->writeln('✱ '.$commitMessage) : $this->io->writeln($commitMessage);

        $parser = new VersionParser($this->service->getVersionFromGit());
        $mutator = new VersionMutator($parser->parse(), $this->options);

        // TODO GitCommand::createTag($version);
        $tagCreatedMessage = sprintf('Created tag "%s".', ''); // TODO
        $this->colors ? $this->io->writeln('✱ '.$tagCreatedMessage) : $this->io->writeln($tagCreatedMessage);

        if ($this->configurations->isGitPushEnabled()) {
            // TODO GitCommand::pushTag($version);
            $updatedMessage = 'Pushed to remote git repository';
            $this->colors ? $this->io->writeln('✔ '.$updatedMessage) : $this->io->writeln($updatedMessage);
        }

        $this->io->newLine();
    }
}
