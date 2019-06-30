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

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
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
     * @var ArrayCollection
     */
    private $metaComponents;

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

        $this->metaComponents = new ArrayCollection();
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
        return $this->getMainComponentValue($type);
    }

    /**
     * @param string $type
     *
     * @return int
     */
    private function getMainComponentValue(string $type): int
    {
        return $this->getMainComponents()->get($type)->getValue();
    }

    /**
     * @return VersionComponentsCollection
     */
    public function getMainComponents(): VersionComponentsCollection
    {
        return $this->mainComponents;
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
     * @return int
     */
    public function getMajor(): int
    {
        return $this->getMainComponentValue(self::MAJOR);
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
     * @return int
     */
    public function getMinor(): int
    {
        return $this->getMainComponentValue(self::MINOR);
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
     * @return int
     */
    public function getPatch(): int
    {
        return $this->getMainComponentValue(self::PATCH);
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
     * @return Version
     */
    public function clearPreRelease(): Version
    {
        $this->preReleaseComponents->disableAll();

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Version
     */
    public function enablePreRelease(string $type): Version
    {
        $this->getPreReleaseComponent($type)->setEnabled(true);

        return $this;
    }

    /**
     * @param string $type
     *
     * @return VersionComponent
     */
    public function getPreReleaseComponent(string $type): VersionComponent
    {
        return $this->preReleaseComponents->getValue($type);
    }

    /**
     * @return null|int
     */
    public function getPreReleaseVersion()
    {
        return $this->getPreReleaseComponent($this->getPreRelease())->getValue();
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
     * @param string $type
     * @param int $value
     */
    public function setPreReleaseVersion(string $type, int $value)
    {
        $this->getPreReleaseComponent($type)->setValue($value);
    }

    /**
     * @return ArrayCollection
     */
    public function getMetaComponents(): ArrayCollection
    {
        return $this->metaComponents;
    }

    /**
     * @param ArrayCollection $metaComponents
     */
    public function setMetaComponents(ArrayCollection $metaComponents)
    {
        $this->metaComponents = $metaComponents;
    }
}
