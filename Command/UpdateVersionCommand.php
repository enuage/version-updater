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
     * @var boolean $options
     */
    private $options = false;

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
        $versionRegex = '(?>'.
            '(?<majorVersion>\d+)\.?'.
            '(?<minorVersion>\d*)\.?'.
            '(?<patchVersion>\d*)'.
            '(?>\-(?<preRelease>alpha|beta|rc)'.
            '(?>\.(?<preReleaseVersion>\d+))?)?'.
            '(?>\+[a-zA-Z\d]+)*'. // Metadata isn't captured
            ')';

        $version = $input->getArgument('version');
        $newVersion = $version;

        $this->hasOptions(false);

        $metadata = $this->getMetadata();

        foreach ($files as $directive) {
            $fileName = key($directive);
            $pattern = $directive[$fileName];

            $pattern = str_replace('\V', $versionRegex, $pattern);
            $filePath = realpath(sprintf('%s/../%s', $root, $fileName));

            $content = file_get_contents($filePath);

            preg_match($pattern, $content, $matches);

            $majorVersion = intval($matches['majorVersion']);
            $minorVersion = intval($matches['minorVersion']);
            $patchVersion = intval($matches['patchVersion']);

            if (!$version) {
                if ($this->hasParameterOption('major')) {
                    $this->isDown() && $majorVersion > 0 ? $majorVersion-- : $majorVersion++;
                    $minorVersion = 0;
                    $patchVersion = 0;
                }

                if ($this->hasParameterOption('minor')) {
                    $this->isDown() && $minorVersion > 0 ? $minorVersion-- : $minorVersion++;
                    $patchVersion = 0;
                }

                if ($this->hasParameterOption('patch')) {
                    $this->isDown() && $patchVersion > 0 ? $patchVersion-- : $patchVersion++;
                }
            }

            if ($minorVersion == 0 && $majorVersion == 0) {
                $minorVersion = 1;
            }

            $preRelease = '';
            if (!$input->hasParameterOption('--release')) {
                $preReleaseVersions = ['alpha', 'beta', 'rc'];
                foreach ($preReleaseVersions as $preReleaseVersion) {
                    if ($this->hasParameterOption($preReleaseVersion)) {
                        $preRelease = $this->setPreRelease($preReleaseVersion, $matches);
                    }
                }
            }

            if ($this->hasOptions()) {
                $newVersion = sprintf(
                    '%d.%d.%d%s%s',
                    $majorVersion,
                    $minorVersion,
                    $patchVersion,
                    $preRelease,
                    $metadata
                );
            }

            unset($majorVersion, $minorVersion, $patchVersion, $preRelease);

            $lastMatch = end($matches);

            // 7 groups + 5 named groups. Fucking PHP doesn't exclude unnamed groups even if exists named groups. Facepalm
            $lastMatch = !$this->isInt($lastMatch) && count($matches) > 12 ? end($matches) : '';

            if (!is_null($newVersion)) {
                $content = preg_replace($pattern, sprintf('${1}%s%s', $newVersion, $lastMatch), $content);

                file_put_contents($filePath, $content);
                $output->writeln(sprintf('<comment>Updated project version in file: %s</comment>', $filePath));
            }

            unset($matches, $filePath, $content, $pattern, $fileName);
        }

        $output->writeln(sprintf('<info>Project version changed to: %s</info>', $newVersion));

        $version = null;
    }

    /**
     * @param bool $options
     *
     * @return bool
     */
    private function hasOptions(?bool $options = null): bool
    {
        if (is_bool($options)) {
            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getMetadata(): string
    {
        $metadata = [];
        if ($this->hasParameterOption('date')) {
            $now = new \DateTime('now');
            $metadata[] = $now->format($this->input->getOption('date') ?? 'c');
        }

        if ($this->hasParameterOption('meta')) {
            $inputMeta = $this->input->getOption('meta');
            if ($inputMeta) {
                $metadata[] = $inputMeta;
            }
        }

        return !empty($metadata) ? '+'.implode("+", $metadata) : '';
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasParameterOption(string $name): bool
    {
        $result = $this->input->hasParameterOption('--'.$name);

        if ($result) {
            $this->hasOptions(true);
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isDown(): bool
    {
        return $this->input->hasParameterOption('--down');
    }

    /**
     * @param string $name alpha|beta|rc
     * @param array $matches
     *
     * @return string
     */
    private function setPreRelease(string $name, array $matches): string
    {
        $preRelease = '-'.$name;
        $preReleaseDefined = isset($matches["preRelease"]) && $matches["preRelease"] == $name;
        $preReleaseVersionDefined = isset($matches["preReleaseVersion"]) && $this->isInt($matches["preReleaseVersion"]);

        if ($preReleaseDefined && !$preReleaseVersionDefined && !$this->isDown()) {
            $preRelease .= '.1';
        }

        if ($preReleaseDefined && $preReleaseVersionDefined) {
            $preReleaseVersion = intval($matches["preReleaseVersion"]);
            $preReleaseVersion = $this->isDown() ? --$preReleaseVersion : ++$preReleaseVersion;

            if ($preReleaseVersion > 0) {
                $preRelease .= '.'.$preReleaseVersion;
            }
        }

        return $preRelease;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isInt(string $value): bool
    {
        return strval(intval($value)) === $value;
    }
}
