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

use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Finder\FilesFinder;
use Enuage\VersionUpdaterBundle\Formatter\FileFormatter;
use Enuage\VersionUpdaterBundle\Handler\AbstractHandler;
use Enuage\VersionUpdaterBundle\Handler\JsonHandler;
use Enuage\VersionUpdaterBundle\Handler\TextHandler;
use Enuage\VersionUpdaterBundle\Handler\YamlHandler;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Parser\AbstractParser;
use Enuage\VersionUpdaterBundle\Parser\CommandOptionsParser;
use Enuage\VersionUpdaterBundle\Parser\FileParser;
use Enuage\VersionUpdaterBundle\Parser\VersionParser;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateVersionCommand
 *
 * @package Enuage\VersionUpdaterBundle\Command
 */
class UpdateVersionCommand extends ContainerAwareCommand
{
    /**
     * @var VersionOptions
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('enuage:version:update');
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
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = CommandOptionsParser::parse($input);

        $finder = new FilesFinder();
        $finder->setRootDirectory($this->getContainer()->getParameter('kernel.root_dir'));

        $finder->setFiles($this->getContainer()->getParameter('enuage_version_updater.files'));
        $this->updateFiles($finder, new TextHandler());

        $finder->setFiles($this->getContainer()->getParameter('enuage_version_updater.json'), 'json');
        $this->updateFiles($finder, new JsonHandler());

        $finder->setFiles($this->getContainer()->getParameter('enuage_version_updater.yaml'), 'yaml');
        $this->updateFiles($finder, new YamlHandler());
    }

    /**
     * @param FilesFinder $finder
     * @param AbstractHandler $handler
     */
    private function updateFiles(FilesFinder $finder, AbstractHandler $handler)
    {
        if ($finder->hasFiles()) {
            $finder->iterate(
                function ($file, $pattern) use ($handler) {
                    $handler->setPattern($pattern);
                    $fileParser = new FileParser($file, $handler);
                    $versionParser = new VersionParser($this->options->getVersion());

                    /** @var AbstractParser $parser */
                    $parser = $this->options->hasVersion() ? $versionParser : $fileParser;
                    $mutator = new VersionMutator($parser->parse(), $this->options);

                    if (!$this->options->hasVersion()) {
                        $mutator->update();
                    }

                    $fileFormatter = new FileFormatter($fileParser);
                    $fileFormatter->setHandler($handler);
                    $fileFormatter->format($mutator->getFormatter());
                }
            );
        }
    }
}
