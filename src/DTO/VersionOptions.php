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
        Version::MAJOR,
        Version::MINOR,
        Version::PATCH,
        'down',
        Version::ALPHA,
        Version::BETA,
        Version::RELEASE_CANDIDATE,
        'release',
        Version::META_DATE,
        Version::META,
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
     * TODO: refactor the code and remove this method
     *
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
     * FIXME
     *
     * This will be broken when user will try to do something like `...->increaseMajor()->decreasePatch()`, they both
     * will be decreased. I suggest to add something like  major = (-1|0|1) and check his value during version update.
     * Its also applicable to another main types of version. The value **SHOULD NOT** be less than -1 because
     * `...->decreaseMajor()->decreaseMajor()->increaseMajor()` will decrease the value (result will be 0-1-1+1=-1,
     * should be 0). Add the private method that will check availability for increment/decrement for prevent code
     * duplication
     *
     * +1: increase version
     * 0: do not modify, default value
     * -1: decrease version
     *
     * @return VersionOptions
     */
    public function increaseMajor(): VersionOptions
    {
        $this->major = true;
        $this->down = false;

        return $this;
    }

    /**
     * FIXME: Check lines 131-144
     *
     * @return VersionOptions
     */
    public function decreaseMajor(): VersionOptions
    {
        $this->major = true;
        $this->down = true;

        return $this;
    }

    /**
     * FIXME: Check lines 131-144
     *
     * @return VersionOptions
     */
    public function increaseMinor(): VersionOptions
    {
        $this->minor = true;
        $this->down = false;

        return $this;
    }

    /**
     * FIXME: Check lines 131-144
     *
     * @return VersionOptions
     */
    public function decreaseMinor(): VersionOptions
    {
        $this->minor = true;
        $this->down = true;

        return $this;
    }

    /**
     * FIXME: Check lines 131-144
     *
     * @return VersionOptions
     */
    public function increasePatch(): VersionOptions
    {
        $this->patch = true;
        $this->down = false;

        return $this;
    }

    /**
     * FIXME: Check lines 131-144
     *
     * @return VersionOptions
     */
    public function decreasePatch(): VersionOptions
    {
        $this->patch = true;
        $this->down = true;

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
        $preReleaseVersions = [
            $this->alpha,
            $this->beta,
            $this->rc,
        ];

        foreach ($preReleaseVersions as $preReleaseVersion) {
            if (true === $preReleaseVersion) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPreReleaseVersionUpdatable(): bool
    {
        return $this->updatePreReleaseVersion;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isMainVersionUpdated(string $name): bool
    {
        if (in_array($name, Version::MAIN_VERSIONS, true)) {
            switch ($name) {
                case Version::MAJOR:
                    return true === $this->major;
                    break;
                case Version::MINOR:
                    return true === $this->minor;
                    break;
                case Version::PATCH:
                    return true === $this->patch;
                    break;
            }
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isPreReleaseVersionUpdated(string $name): bool
    {
        if (in_array($name, Version::PRE_RELEASE_VERSIONS, true)) {
            switch ($name) {
                case Version::ALPHA:
                    return true === $this->alpha;
                    break;
                case Version::BETA:
                    return true === $this->beta;
                    break;
                case Version::RELEASE_CANDIDATE:
                    return true === $this->rc;
                    break;
            }
        }

        return false;
    }
}
