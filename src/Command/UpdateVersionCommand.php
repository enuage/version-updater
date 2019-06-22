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

use Enuage\VersionUpdaterBundle\Formatter\FileFormatter;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Parser\AbstractParser;
use Enuage\VersionUpdaterBundle\Parser\FileParser;
use Enuage\VersionUpdaterBundle\Parser\VersionParser;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class UpdateVersionCommand
 *
 * @package Enuage\VersionUpdaterBundle\Command
 */
class UpdateVersionCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface $input
     */
    private $input;

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

        $this->addOption('down', null, InputOption::VALUE_NONE,
            'Decrease version. It\'s also applicable to prerelease versions');

        // Prerelease versions
        $this->addOption('alpha', null, InputOption::VALUE_NONE, 'Increase or define the alpha version');
        $this->addOption('beta', null, InputOption::VALUE_NONE, 'Increase or define the beta version');
        $this->addOption('rc', null, InputOption::VALUE_NONE, 'Increase or define the release candidate version');
        $this->addOption('release', null, InputOption::VALUE_NONE, 'Remove all prerelease versions');

        // Metadata
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Add date metadata to the version');
        $this->addOption('meta', null, InputOption::VALUE_OPTIONAL, 'Add metadata to the version');

//        $this->addOption('composer', null, InputOption::VALUE_NONE, 'Update composer file');
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $isDown = $this->hasParameterOption('down');
        $isDateMetaDefined = $this->hasParameterOption(Version::META_DATE);
        $isMetaDefined = $this->hasParameterOption(Version::META);
        $isRelease = $this->hasParameterOption('release');

        $version = $input->getArgument('version');
        $newVersion = $version;

        if (!($files = $this->getFilesArray())) {
            $output->writeln('<info>No files found for update.</info>');
            exit(1);
        }

        foreach ($files as $path => $pattern) {
            $file = $this->getFile($path);

            $fileParser = new FileParser($file, $pattern);
            $versionParser = new VersionParser($version);

            /** @var AbstractParser $parser */
            $parser = $version ? $versionParser : $fileParser;
            $mutator = new VersionMutator($parser->parse());

            if (!$version) {
                $mutator->setDown($isDown);

                if ($isDateMetaDefined) {
                    $mutator->enableDateMeta($this->input->getOption(Version::META_DATE));
                }

                if ($isMetaDefined) {
                    $mutator->enableMeta($this->input->getOption(Version::META));
                }

                foreach (Version::MAIN_VERSIONS as $version) {
                    if ($this->hasParameterOption($version)) {
                        $mutator->updateVersion($version);
                    }
                }

                if (!$isRelease) {
                    $preReleaseOptions = [];
                    foreach (Version::PRE_RELEASE_VERSIONS as $preReleaseVersion) {
                        $preReleaseOptions[$preReleaseVersion] = $this->hasParameterOption($preReleaseVersion);
                    }

                    $mutator->updatePreRelease($preReleaseOptions);
                } else {
                    $mutator->release();
                }
            }

            $versionFormatter = new VersionFormatter();
            $versionFormatter->setMutator($mutator);

            $fileFormatter = new FileFormatter($fileParser);
            $fileFormatter->format($versionFormatter);
        }

        $output->writeln(sprintf('<info>Project version changed to: %s</info>', $newVersion));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasParameterOption(string $name): bool
    {
        return $this->input->hasParameterOption('--'.$name);
    }

    /**
     * Regenerate files array to array<filePath, pattern>
     *
     * Input:
     * ```php
     * [
     *    0 => [
     *        ".env" => "/^(API_VERSION=)\V/m",
     *    ],
     *    1 => [
     *        "README.md" => "/^(Version:\s)\V/m",
     *    ],
     * ]
     * ```
     *
     * Output:
     * ```
     * [
     *     ".env" => "/^(API_VERSION=)\V/m",
     *     "README.md" => "/^(Version:\s)\V/m",
     * ]
     * ```
     *
     * @return array
     */
    private function getFilesArray(): array
    {
        $files = [];
        $filesArray = $this->getContainer()->getParameter('enuage_version_updater.files');
        if (!empty($filesArray)) {
            array_walk_recursive($filesArray, static function ($value, $key) use (&$files) {
                if (!is_numeric($key)) {
                    $files[$key] = $value;
                }
            });
        }

        return $files;
    }

    /**
     * @param string $fileName
     *
     * @return SplFileInfo
     */
    private function getFile(string $fileName): SplFileInfo
    {
        $root = $this->getContainer()->getParameter('kernel.root_dir');
        $filePath = explode('/', $fileName);

        $lastIndex = count($filePath) - 1;
        $fileName = $filePath[$lastIndex];
        unset($filePath[$lastIndex]);

        $filePath = array_merge([$root, '..'], $filePath);
        $filePath = implode('/', $filePath).'/';

        $finder = new Finder();
        $finder->files()->in($filePath)->name($fileName);

        return array_values(iterator_to_array($finder->getIterator()))[0];
    }
}
