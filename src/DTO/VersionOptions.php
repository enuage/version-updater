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
use Enuage\VersionUpdaterBundle\ValueObject\VersionModifier;
use Exception;

/**
 * Class VersionOptions
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionOptions
{
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
     * @var VersionModifier
     */
    private $preReleaseVersionModifier;

    /**
     * @var ArrayCollection
     */
    private $metaComponents;

    /**
     * VersionOptions constructor.
     */
    public function __construct()
    {
        $this->mainTypes = new VersionModifierCollection(
            Version::MAIN_VERSIONS,
            VersionModifierCollection::ENABLE_ALL
        );
        $this->preReleaseTypes = new VersionModifierCollection(Version::PRE_RELEASE_VERSIONS);
        $this->metaComponents = new ArrayCollection();
        $this->preReleaseVersionModifier = new VersionModifier();
        $this->preReleaseVersionModifier->enable();
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
        $this->getMainTypes()->increase(Version::MAJOR);

        return $this;
    }

    /**
     * @return VersionModifierCollection
     */
    public function getMainTypes(): VersionModifierCollection
    {
        return $this->mainTypes;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMajor(): VersionOptions
    {
        $this->getMainTypes()->decrease(Version::MAJOR);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increaseMinor(): VersionOptions
    {
        $this->getMainTypes()->increase(Version::MINOR);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreaseMinor(): VersionOptions
    {
        $this->getMainTypes()->decrease(Version::MINOR);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function increasePatch(): VersionOptions
    {
        $this->getMainTypes()->increase(Version::PATCH);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePatch(): VersionOptions
    {
        $this->getMainTypes()->decrease(Version::PATCH);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function updateAlpha(): VersionOptions
    {
        $this->getPreReleaseTypes()->enable(Version::ALPHA);

        return $this;
    }

    /**
     * @return VersionModifierCollection
     */
    public function getPreReleaseTypes(): VersionModifierCollection
    {
        return $this->preReleaseTypes;
    }

    /**
     * @return VersionOptions
     */
    public function updateBeta(): VersionOptions
    {
        $this->getPreReleaseTypes()->enable(Version::BETA);

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function updateReleaseCandidate(): VersionOptions
    {
        $this->getPreReleaseTypes()->enable(Version::RELEASE_CANDIDATE);

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
    private function isPreReleaseDowngrade(): bool
    {
        return $this->getPreReleaseVersionModifier()->isDowngrade();
    }

    /**
     * @return VersionModifier
     */
    public function getPreReleaseVersionModifier(): VersionModifier
    {
        return $this->preReleaseVersionModifier;
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
     * @param string $type
     *
     * @return VersionModifier|null
     */
    public function getMainType(string $type)
    {
        return $this->getMainTypes()->getValue($type);
    }

    /**
     * @return VersionOptions
     */
    public function increasePreRelease(): VersionOptions
    {
        $this->getPreReleaseVersionModifier()->setDowngrade(false)->update();

        return $this;
    }

    /**
     * @return VersionOptions
     */
    public function decreasePreRelease(): VersionOptions
    {
        $this->getPreReleaseVersionModifier()->setDowngrade(true)->update();

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPreRelease(): bool
    {
        foreach ($this->getPreReleaseTypes()->getIterator() as $type) {
            if ($type->isEnabled()) {
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
    public function isMainVersionUpdated(string $name): bool
    {
        return $this->getMainTypes()->get($name)->isUpdated();
    }

    /**
     * @return ArrayCollection
     */
    public function getMetaComponents(): ArrayCollection
    {
        return $this->metaComponents;
    }

    /**
     * @return void
     */
    public function release()
    {
        $this->release = true;

        /** @var VersionModifier $type */
        foreach ($this->getPreReleaseTypes()->getIterator() as $type) {
            $type->disable();
        }
    }
}
