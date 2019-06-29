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

use Enuage\VersionUpdaterBundle\Collection\VersionModifierCollection;
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
     * @var VersionModifierCollection
     */
    private $mainVersions;

    /**
     * @var bool
     */
    private $down = false;

    /**
     * @var bool
     */
    private $alphaEnabled = false;

    /**
     * @var bool
     */
    private $betaEnabled = false;

    /**
     * Release candidate
     *
     * @var bool
     */
    private $rcEnabled = false;

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
    private $dateEnabled = false;

    /**
     * @var string
     */
    private $dateFormat = 'c';

    /**
     * @var bool
     */
    private $metaEnabled = false;

    /**
     * @var string|null
     */
    private $metaValue;

    /**
     * VersionOptions constructor.
     */
    public function __construct()
    {
        $this->mainVersions = new VersionModifierCollection(Version::MAIN_VERSIONS, true);
    }

    /**
     * TODO: refactor the code
     *
     * @param string $option
     *
     * @return VersionOptions
     */
    public function enable(string $option): VersionOptions
    {
        $this->mainVersions->setDowngrade($this->down);

        if (in_array($option, self::OPTIONS, true)) {
            if (in_array($option, Version::MAIN_VERSIONS, true)) {
                $this->mainVersions->get($option)->update();
            }

            switch ($option) {
                case Version::ALPHA:
                    $this->alphaEnabled = true;
                    break;
                case Version::BETA:
                    $this->betaEnabled = true;
                    break;
                case Version::RELEASE_CANDIDATE:
                    $this->rcEnabled = true;
                    break;
                case 'release':
                    $this->release = true;
                    break;
                case Version::META_DATE:
                    $this->dateEnabled = true;
                    break;
                case Version::META:
                    $this->metaEnabled = true;
                    break;
            }

        }

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increaseMajor(): VersionOptions
    {
        $this->mainVersions->get(Version::MAJOR)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMajor(): VersionOptions
    {
        $this->mainVersions->get(Version::MAJOR)->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increaseMinor(): VersionOptions
    {
        $this->mainVersions->get(Version::MINOR)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMinor(): VersionOptions
    {
        $this->mainVersions->get(Version::MINOR)->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increasePatch(): VersionOptions
    {
        $this->mainVersions->get(Version::PATCH)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePatch(): VersionOptions
    {
        $this->mainVersions->get(Version::PATCH)->setDowngrade(true)->update();

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
        $this->alphaEnabled = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return VersionOptions
     */
    public function updateBeta(bool $value): VersionOptions
    {
        $this->betaEnabled = $value;

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
        return $this->dateEnabled;
    }

    /**
     * @return bool
     */
    public function isMetaDefined(): bool
    {
        return $this->metaEnabled;
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
        $this->rcEnabled = $value;

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
            $this->alphaEnabled,
            $this->betaEnabled,
            $this->rcEnabled,
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
        return $this->mainVersions->get($name)->isUpdated();
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
                    return true === $this->alphaEnabled;
                    break;
                case Version::BETA:
                    return true === $this->betaEnabled;
                    break;
                case Version::RELEASE_CANDIDATE:
                    return true === $this->rcEnabled;
                    break;
            }
        }

        return false;
    }

    /**
     * @param null|string $version
     */
    public function setVersion(string $version = null)
    {
        $this->version = $version;
    }

    /**
     * @return VersionModifierCollection
     */
    public function getMainVersions(): VersionModifierCollection
    {
        return $this->mainVersions;
    }
}
