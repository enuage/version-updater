<?php
/**
 * Version
 *
 * Created at 2019-06-22 12:26 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\ValueObject;

use DateTime;
use Enuage\VersionUpdaterBundle\Collection\VersionComponentsCollection;

/**
 * Class Version
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class Version
{
    const MAJOR = 'major';
    const MINOR = 'minor';
    const PATCH = 'patch';

    const MAIN_VERSIONS = [
        self::MAJOR,
        self::MINOR,
        self::PATCH,
    ];

    const ALPHA = 'alpha';
    const BETA = 'beta';
    const RELEASE_CANDIDATE = 'rc';

    const PRE_RELEASE_VERSIONS = [
        self::ALPHA,
        self::BETA,
        self::RELEASE_CANDIDATE,
    ];

    const META = 'meta';
    const META_DATE = 'date';

    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @var VersionComponentsCollection
     */
    private $mainComponents;

    /**
     * @var VersionComponentsCollection
     */
    private $preReleaseComponents;

    /**
     * @var int|null
     */
    private $preReleaseVersion;

    /**
     * @var bool
     */
    private $dateMeta = false;

    /**
     * @var DateTime
     */
    private $dateMetaValue;

    /**
     * @var string
     */
    private $dateMetaFormat = 'c';

    /**
     * @var bool
     */
    private $meta = false;

    /**
     * @var string|null
     */
    private $metaValue;

    /**
     * Version constructor.
     */
    public function __construct()
    {
        $this->mainComponents = new VersionComponentsCollection(self::MAIN_VERSIONS);
        $this->preReleaseComponents = new VersionComponentsCollection(
            self::PRE_RELEASE_VERSIONS,
            VersionComponentsCollection::DISABLE_ALL
        );
    }

    /**
     * @return null|string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $value
     *
     * @return Version
     */
    public function setPrefix(string $value): Version
    {
        $this->prefix = $value;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return null|int
     */
    public function getMainVersion(string $type)
    {
        return $this->mainComponents->get($type)->getValue();
    }

    /**
     * @param string $type
     * @param int $value
     *
     * @return Version
     */
    public function setMainVersion(string $type, int $value): Version
    {
        $this->mainComponents->set($type, $value);

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Version
     */
    public function setMajor(int $value): Version
    {
        $this->mainComponents->set(self::MAJOR, $value);

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Version
     */
    public function setMinor(int $value): Version
    {
        $this->mainComponents->set(self::MINOR, $value);

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Version
     */
    public function setPatch(int $value): Version
    {
        $this->mainComponents->set(self::PATCH, $value);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPreRelease()
    {
        /** @var VersionComponent $component */
        foreach ($this->preReleaseComponents->getIterator() as $type => $component) {
            if ($component->isEnabled()) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @return Version
     */
    public function clearPreRelease(): Version
    {
        foreach (self::PRE_RELEASE_VERSIONS as $version) {
            $this->preReleaseComponents->get($version)->setEnabled(false);
        }

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Version
     */
    public function enablePreRelease(string $type): Version
    {
        $this->preReleaseComponents->get($type)->setEnabled(true);

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Version
     */
    public function disablePreRelease(string $type): Version
    {
        $this->preReleaseComponents->get($type)->setEnabled(false);

        return $this;
    }

    /**
     * @return null|int
     */
    public function getPreReleaseVersion()
    {
        return $this->preReleaseVersion;
    }

    /**
     * @param null|int $preReleaseVersion
     *
     * @return Version
     */
    public function setPreReleaseVersion(int $preReleaseVersion): Version
    {
        $this->preReleaseVersion = $preReleaseVersion;

        return $this;
    }

    /**
     * @return Version
     */
    public function clearPreReleaseVersion(): Version
    {
        $this->preReleaseVersion = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDateMetaDefined(): bool
    {
        return $this->dateMeta;
    }

    /**
     * @param bool $isDefined
     *
     * @return Version
     */
    public function setDateMeta(bool $isDefined): Version
    {
        $this->dateMeta = $isDefined;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateMetaFormat(): string
    {
        return $this->dateMetaFormat;
    }

    /**
     * @param string $format
     *
     * @return Version
     */
    public function setDateMetaFormat(string $format): Version
    {
        $this->dateMetaFormat = $format;

        return $this;
    }

    /**
     * @param bool $isDefined
     *
     * @return Version
     */
    public function setMeta(bool $isDefined): Version
    {
        $this->meta = $isDefined;

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
     * @param null|string $value
     *
     * @return Version
     */
    public function setMetaValue(string $value): Version
    {
        $this->metaValue = $value;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateMetaValue(): DateTime
    {
        return $this->dateMetaValue;
    }

    /**
     * @param DateTime $value
     *
     * @return Version
     */
    public function setDateMetaValue(DateTime $value): Version
    {
        $this->dateMetaValue = $value;

        return $this;
    }

    /**
     * @return VersionComponentsCollection
     */
    public function getMainComponents(): VersionComponentsCollection
    {
        return $this->mainComponents;
    }
}
