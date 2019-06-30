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
use Enuage\VersionUpdaterBundle\Collection\VersionModifierCollection;
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\ValueObject\MetaComponent;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Enuage\VersionUpdaterBundle\ValueObject\VersionModifier;
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
            if ($this->options->isMainVersionUpdated($version)) {
                $this->updateMainVersion($version);
            }
        }

        if (!$this->options->isRelease()) {
            $this->updatePreRelease($this->options->getPreReleaseTypes());
        } else {
            $this->clearPreRelease();
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
    private function updateMainVersion(string $key)
    {
        $value = $this->version->getMainVersion($key) + $this->options->getMainTypes()->get($key)->getModifier();

        if ($value < 0) {
            $value = 0;
        }

        if (0 === $value && Version::MAJOR === $key) {
            $this->version->setMinor(1);
        }

        $this->version->setMainVersion($key, $value);
    }

    /**
     * @param VersionModifierCollection $types
     */
    private function updatePreRelease(VersionModifierCollection $types)
    {
        $definedType = $this->version->getPreRelease();
        $this->version->clearPreRelease();

        $version = $this->version->getPreReleaseVersion();
        $this->version->clearPreReleaseVersion();
        $isVersionDefined = null !== $version && is_numeric($version);

        /** @var VersionModifier $modifier */
        foreach ($types as $type => $modifier) {
            $isApplicable = $definedType === $type;

            if ($modifier->isEnabled()) {
                $this->version
                    ->clearPreRelease()
                    ->clearPreReleaseVersion()
                    ->enablePreRelease($type);

                if (!$isApplicable && $this->options->isDowngrade()) {
                    $this->clearPreRelease();
                }

                if ($isApplicable && !$isVersionDefined) {
                    if ($this->options->isDowngrade()) {
                        $this->version
                            ->disablePreRelease($type)
                            ->clearPreReleaseVersion();
                    } else {
                        $this->updatePreReleaseVersion(1);
                    }
                }

                if ($isApplicable && $isVersionDefined) {
                    $this->updatePreReleaseVersion(
                        $version,
                        $modifier->update()->getModifier()
                    );
                }
            }
        }

        if (null !== $definedType && $this->options->isPreReleaseVersionUpdatable()) {
            $isDowngrade = $this->options->isPreReleaseDowngrade();

            $modifier = new VersionModifier(true);
            $modifier->setDowngrade($isDowngrade);

            $this->version->enablePreRelease($definedType);
            $this->updatePreReleaseVersion($version ?? 0, $modifier->update()->getModifier());

            if ((null === $version || $version <= 0) && $isDowngrade) {
                $this->clearPreRelease();
            }
        }
    }

    /**
     * @return void
     */
    private function clearPreRelease()
    {
        $this->version->clearPreRelease()->clearPreReleaseVersion();
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
     * @param string $format
     *
     * @return VersionMutator
     * @throws Exception
     */
    private function enableDateMeta(string $format): self
    {
        $metaComponent = new MetaComponent();
        $metaComponent->setType(MetaComponent::TYPE_DATETIME);
        $metaComponent->setValue(new DateTime());
        $metaComponent->setFormat($format);

        $this->version->getMetaComponents()->add($metaComponent);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return VersionMutator
     */
    private function enableMeta(string $value): self
    {
        $metaComponent = new MetaComponent();
        $metaComponent->setValue($value);

        $this->version->getMetaComponents()->add($metaComponent);

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
