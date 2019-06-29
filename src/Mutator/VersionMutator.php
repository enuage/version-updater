<?php
/**
 * VersionMutator
 *
 * Created at 2019-06-22 2:24 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Mutator;

use DateTime;
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;

/**
 * Class VersionMutator
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionMutator
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @var VersionOptions
     */
    private $options;

    /**
     * VersionMutator constructor.
     *
     * @param Version $version
     * @param VersionOptions $options
     */
    public function __construct(Version $version, VersionOptions $options)
    {
        $this->version = $version;
        $this->options = $options;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function update(): self
    {
        foreach (Version::MAIN_VERSIONS as $version) {
            if ($this->options->has($version)) {
                $this->updateVersion($version);
            }
        }

        if (!$this->options->isRelease()) {
            $preReleaseOptions = [];
            foreach (Version::PRE_RELEASE_VERSIONS as $preReleaseVersion) {
                $preReleaseOptions[$preReleaseVersion] = $this->options->has($preReleaseVersion);
            }

            $this->updatePreRelease($preReleaseOptions);
        } else {
            $this->release();
        }

        if ($this->options->isDateDefined()) {
            $this->enableDateMeta($this->options->getDateFormat());
        }

        if ($this->options->isMetaDefined()) {
            $this->enableMeta($this->options->getMetaValue());
        }

        return $this;
    }

    /**
     * @param string $key
     */
    private function updateVersion(string $key)
    {
        $value = $this->version->getVersion($key);

        if ($this->options->isDowngrade()) {
            $value > 0 ? $value-- : $value = 0;

            if (0 === $value && Version::MAJOR === $key) {
                $this->version->setMinor(1);
            }
        } else {
            $value++;
        }

        $this->version->setMainVersion($key, $value);
    }

    /**
     * @param array $preReleaseOptions
     */
    private function updatePreRelease(array $preReleaseOptions)
    {
        $preRelease = $this->version->getPreRelease();
        $this->version->clearPreRelease();

        $preReleaseVersion = $this->version->getPreReleaseVersion();
        $this->version->clearPreReleaseVersion();
        $isPreReleaseVersionDefined = null !== $preReleaseVersion && is_numeric($preReleaseVersion);

        foreach ($preReleaseOptions as $preReleaseOption => $isOptionEnabled) {
            $isPreReleaseDefined = $preRelease === $preReleaseOption;

            if ($isOptionEnabled) {
                $this->version
                    ->clearPreRelease()
                    ->clearPreReleaseVersion()
                    ->setPreRelease($preReleaseOption);

                if (!$isPreReleaseDefined && $this->options->isDowngrade()) {
                    $this->release();
                }

                if ($isPreReleaseDefined && !$isPreReleaseVersionDefined) {
                    if ($this->options->isDowngrade()) {
                        $this->version
                            ->setPreRelease($preReleaseOption, false)
                            ->clearPreReleaseVersion();
                    } else {
                        $this->updatePreReleaseVersion(1);
                    }
                }

                if ($isPreReleaseDefined && $isPreReleaseVersionDefined) {
                    $this->updatePreReleaseVersion(
                        $preReleaseVersion,
                        $this->options->isPreReleaseDowngrade() ? -1 : 1
                    );
                }
            }
        }

        if (null !== $preRelease && !$this->options->isRelease() && $this->options->isPreReleaseVersionUpdatable()) {
            $isDowngrade = $this->options->isPreReleaseDowngrade();

            $this->version->setPreRelease($preRelease);
            $this->updatePreReleaseVersion($preReleaseVersion ?? 0, $isDowngrade ? -1 : 1);

            if ((null === $preReleaseVersion || $preReleaseVersion < 1) && $isDowngrade) {
                $this->release();
            }
        }
    }

    /**
     * @param int $value
     * @param int $modifier
     *
     * @return VersionMutator
     */
    private function updatePreReleaseVersion(int $value, int $modifier = null): VersionMutator
    {
        if ($modifier) {
            $value += $modifier;
        }

        if ($value && $value > 0) {
            $this->version->setPreReleaseVersion($value);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function release()
    {
        $this->version->clearPreRelease()->clearPreReleaseVersion();
    }

    /**
     * @param string $format
     *
     * @return VersionMutator
     * @throws Exception
     */
    private function enableDateMeta(string $format): self
    {
        $this->version->setDateMeta(true);
        $this->version->setDateMetaValue(new DateTime());
        $this->version->setDateMetaFormat($format);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return VersionMutator
     */
    private function enableMeta(string $value): self
    {
        $this->version->setMeta(true);
        $this->version->setMetaValue($value);

        return $this;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        $formatter = new VersionFormatter();

        return $formatter->setVersion($this->getVersion());
    }

    /**
     * @return Version
     */
    public function getVersion(): Version
    {
        return $this->version;
    }
}
