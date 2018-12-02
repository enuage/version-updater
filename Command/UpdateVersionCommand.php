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
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        $root = $this->getContainer()->getParameter('kernel.root_dir');
        $files = $this->getContainer()->getParameter('enuage_version_updater.files');
        $versionRegex = '((\d+)\.?(\d*)\.?(\d*)(?>\-(alpha|beta|rc)(?>\.(\d+))?)?(?>\+[a-zA-Z\d]+)*)';

        $version = $input->getArgument('version');

        $options = 0;
        foreach ($files as $directive) {
            $fileName = key($directive);
            $pattern = $directive[$fileName];

            $pattern = str_replace('\V', $versionRegex, $pattern);
            $filePath = realpath(sprintf('%s/../%s', $root, $fileName));

            $content = file_get_contents($filePath);

            preg_match($pattern, $content, $matches);
            $majorVersion = intval($matches[3]);
            $minorVersion = intval($matches[4]);
            $patchVersion = intval($matches[5]);

            if(!$version) {
                if ($input->hasParameterOption('--major')) {
                    $this->isDown() ? $majorVersion-- : $majorVersion++;
                    $minorVersion = 0;
                    $patchVersion = 0;

                    $options++;
                }

                if ($input->hasParameterOption('--minor')) {
                    $this->isDown() ? $minorVersion-- : $minorVersion++;
                    $patchVersion = 0;

                    $options++;
                }

                if ($input->hasParameterOption('--patch')) {
                    $this->isDown() ? $patchVersion-- : $patchVersion++;

                    $options++;
                }
            }

            $preRelease = '';
            if (!$input->hasParameterOption('--release')) {
                $preReleaseVersions = ['alpha', 'beta', 'rc'];
                if (isset($matches[6]) && in_array($matches[6], $preReleaseVersions)) {
                    $preRelease = '-'.$matches[6];
                    if (isset($matches[7]) && $this->isInt($matches[7])) {
                        $preRelease .= '.'.$matches[7];
                    }
                }

                foreach ($preReleaseVersions as $preReleaseVersion) {
                    if ($input->hasParameterOption('--'.$preReleaseVersion)) {
                        $preRelease = $this->updatePreRelease($preReleaseVersion, $matches);

                        $options++;
                    }
                }
            }

            $metadata = '';
            if ($input->hasParameterOption('--date')) {
                $format = $input->getOption('date') ?? 'c';
                $now = new \DateTime('now');
                $metadata = '+'.$now->format($format);

                $options++;
            }

            if ($input->hasParameterOption('--meta')) {
                $inputMeta = $input->getOption('meta');
                if (!empty($inputMeta)) {
                    $metadata .= '+'.$inputMeta;
                }

                $options++;
            }

            if ($minorVersion == 0 && $majorVersion == 0) {
                $minorVersion++;
            }

            if ($options) {
                $version = sprintf('%d.%d.%d%s%s', $majorVersion, $minorVersion, $patchVersion, $preRelease, $metadata);
            }

            $lastMatch = end($matches);
            $lastMatch = !$this->isInt($lastMatch) && count($matches) > 8 ? '${'.(count($matches) - 1).'}' : '';

            if (!is_null($version)) {
                $content = preg_replace($pattern, sprintf('${1}%s%s', $version, $lastMatch), $content);

                file_put_contents($filePath, $content);
                $output->writeln(sprintf('<comment>Updated project version in file: %s</comment>', $filePath));
            }
        }

        $output->writeln(sprintf('<info>Project version changed to: %s</info>', $version));
    }

    /**
     * @return bool
     */
    private function isDown(): bool
    {
        return $this->input->hasParameterOption('--down');
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function isInt($value)
    {
        return (string)(int)$value === $value;
    }

    /**
     * @param string $name alpha|beta|rc
     * @param array $matches
     *
     * @return string
     */
    private function updatePreRelease(string $name, array $matches): string
    {
        $preRelease = '-'.$name;
        $isPreReleaseVersionDefined = isset($matches[7]) && $this->isInt($matches[7]);

        if (isset($matches[6]) && $matches[6] == $name && !$this->isDown() && !$isPreReleaseVersionDefined) {
            $preRelease .= '.1';
        }

        if ($isPreReleaseVersionDefined && $matches[6] == $name) {
            $preReleaseVersion = intval($matches[7]);
            $preReleaseVersion = $this->isDown() ? --$preReleaseVersion : ++$preReleaseVersion;
            if ($preReleaseVersion > 0) {
                $preRelease .= '.'.$preReleaseVersion;
            }
        }

        return $preRelease;
    }
}
