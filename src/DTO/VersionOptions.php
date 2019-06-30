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

use DateTime;
use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\Collection\VersionModifierCollection;
use Enuage\VersionUpdaterBundle\ValueObject\MetaComponent;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;

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
    private $mainTypes;

    /**
     * @var VersionModifierCollection
     */
    private $preReleaseTypes;

    /**
     * @var bool
     */
    private $down = false;

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
     * @var ArrayCollection
     */
    private $metaComponents;

    /**
     * VersionOptions constructor.
     */
    public function __construct()
    {
        $this->mainTypes = new VersionModifierCollection(Version::MAIN_VERSIONS, true);
        $this->preReleaseTypes = new VersionModifierCollection(Version::PRE_RELEASE_VERSIONS);
        $this->metaComponents = new ArrayCollection();
    }

    /**
     * TODO: refactor the code
     *
     * @param string $option
     *
     * @return VersionOptions
     *
     * @throws Exception
     */
    public function enable(string $option): VersionOptions
    {
        $this->mainTypes->setDowngrade($this->down);
        $this->preReleaseTypes->setDowngrade($this->down);

        if (in_array($option, Version::MAIN_VERSIONS, true)) {
            $this->mainTypes->get($option)->update();
        }

        if ('release' === $option) {
            $this->release = true;
        }

        if (in_array($option, Version::PRE_RELEASE_VERSIONS, true)) {
            $this->preReleaseTypes->get($option)->enable();
        }

        if (Version::META_DATE === $option) {
            $this->addDateMeta();
        }

        if (Version::META === $option) {
            $this->addMeta();
        }

        return $this;
    }

    /**
     * @param string|null $format
     *
     * @return $this
     *
     * @throws Exception
     */
    public function addDateMeta(string $format = null): VersionOptions
    {
        $metaComponent = new MetaComponent();
        $metaComponent->setType(MetaComponent::TYPE_DATETIME);
        $metaComponent->setValue(new DateTime());
        $metaComponent->setFormat($format ?? 'c');

        $this->metaComponents->set(Version::META_DATE, $metaComponent);

        return $this;
    }

    /**
     * @param string|null $value
     *
     * @return $this
     *
     * @throws Exception
     */
    public function addMeta(string $value = null): VersionOptions
    {
        $metaComponent = new MetaComponent();
        $metaComponent->setValue($value);

        $this->metaComponents->set(Version::META, $metaComponent);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increaseMajor(): VersionOptions
    {
        $this->mainTypes->get(Version::MAJOR)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMajor(): VersionOptions
    {
        $this->mainTypes->get(Version::MAJOR)->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increaseMinor(): VersionOptions
    {
        $this->mainTypes->get(Version::MINOR)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMinor(): VersionOptions
    {
        $this->mainTypes->get(Version::MINOR)->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increasePatch(): VersionOptions
    {
        $this->mainTypes->get(Version::PATCH)->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePatch(): VersionOptions
    {
        $this->mainTypes->get(Version::PATCH)->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function updateAlpha(): VersionOptions
    {
        $this->preReleaseTypes->get(Version::ALPHA)->enable();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function updateBeta(): VersionOptions
    {
        $this->preReleaseTypes->get(Version::BETA)->enable();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function updateReleaseCandidate(): VersionOptions
    {
        $this->preReleaseTypes->get(Version::RELEASE_CANDIDATE)->enable();

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
     * @return bool
     */
    public function isRelease(): bool
    {
        return $this->release;
    }

    /**
     * @return null|string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param null|string $version
     */
    public function setVersion(string $version = null)
    {
        $this->version = $version;
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
     * @return VersionOptions
     */
    public function increasePreRelease(): VersionOptions
    {
        $this->increasePreRelease = true;
        $this->updatePreReleaseVersion = true;

        $this->preReleaseTypes->setDowngrade(false);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePreRelease(): VersionOptions
    {
        $this->increasePreRelease = false;
        $this->updatePreReleaseVersion = true;

        $this->preReleaseTypes->setDowngrade(true);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPreRelease(): bool
    {
        foreach (Version::PRE_RELEASE_VERSIONS as $type) {
            if ($this->preReleaseTypes->get($type)->isEnabled()) {
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
        return $this->mainTypes->get($name)->isUpdated();
    }

    /**
     * @return VersionModifierCollection
     */
    public function getMainTypes(): VersionModifierCollection
    {
        return $this->mainTypes;
    }

    /**
     * @return VersionModifierCollection
     */
    public function getPreReleaseTypes(): VersionModifierCollection
    {
        return $this->preReleaseTypes;
    }

    /**
     * @return ArrayCollection
     */
    public function getMetaComponents(): ArrayCollection
    {
        return $this->metaComponents;
    }
}
