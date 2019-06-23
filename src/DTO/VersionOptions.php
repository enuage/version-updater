<?php
/**
 * VersionOptions
 *
 * Created at 2019-06-23 2:41 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\DTO;

use Enuage\VersionUpdaterBundle\ValueObject\Version;

/**
 * Class VersionOptions
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionOptions
{
    const OPTIONS = [
        'major',
        'minor',
        'patch',
        'down',
        'alpha',
        'beta',
        'rc',
        'release',
        'date',
        'meta',
    ];

    /**
     * @var null|string
     */
    private $version;

    /**
     * @var bool
     */
    private $major = false;

    /**
     * @var bool
     */
    private $minor = false;

    /**
     * @var bool
     */
    private $patch = false;

    /**
     * @var bool
     */
    private $down = false;

    /**
     * @var bool
     */
    private $alpha = false;

    /**
     * @var bool
     */
    private $beta = false;

    /**
     * Release candidate
     *
     * @var bool
     */
    private $rc = false;

    /**
     * @var bool
     */
    private $release = false;

    /**
     * @var bool
     */
    private $increasePreRelease = true;

    /**
     * @var bool
     */
    private $updatePreReleaseVersion = false;

    /**
     * @var bool
     */
    private $date = false;

    /**
     * @var string
     */
    private $dateFormat = 'c';

    /**
     * @var bool
     */
    private $meta = false;

    /**
     * @var string|null
     */
    private $metaValue;

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return VersionOptions
     */
    public function set(string $name, $value): VersionOptions
    {
        $this->{$name} = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateMajor(bool $value): VersionOptions
    {
        $this->major = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateMinor(bool $value): VersionOptions
    {
        $this->minor = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updatePatch(bool $value): VersionOptions
    {
        $this->patch = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDowngrade(): bool
    {
        return $this->down || $this->isPreReleaseDowngrade();
    }

    /**
     * @return bool
     */
    public function isPreReleaseDowngrade(): bool
    {
        return false === $this->increasePreRelease;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateAlpha(bool $value): VersionOptions
    {
        $this->alpha = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateBeta(bool $value): VersionOptions
    {
        $this->beta = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRelease(): bool
    {
        return $this->release;
    }

    /**
     * @return bool
     */
    public function isDateDefined(): bool
    {
        return $this->date;
    }

    /**
     * @return bool
     */
    public function isMetaDefined(): bool
    {
        return $this->meta;
    }

    /**
     * @return null|string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function hasVersion(): bool
    {
        return null !== $this->version;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function downgrade(bool $value = true): VersionOptions
    {
        $this->down = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateReleaseCandidate(bool $value): VersionOptions
    {
        $this->rc = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     *
     * @return VersionOptions
     */
    public function setDateFormat(string $dateFormat): VersionOptions
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param null|string $metaValue
     *
     * @return VersionOptions
     */
    public function setMetaValue(string $metaValue): VersionOptions
    {
        $this->metaValue = $metaValue;

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increasePreRelease(): VersionOptions
    {
        $this->increasePreRelease = true;
        $this->updatePreReleaseVersion = true;

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePreRelease(): VersionOptions
    {
        $this->increasePreRelease = false;
        $this->updatePreReleaseVersion = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPreRelease(): bool
    {
        foreach (Version::PRE_RELEASE_VERSIONS as $preReleaseVersion) {
            if ($this->has($preReleaseVersion)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        $property = $this->{$name};

        return null !== $property && false !== $property;
    }

    /**
     * @return bool
     */
    public function isPreReleaseVersionUpdatable(): bool
    {
        return $this->updatePreReleaseVersion;
    }
}
