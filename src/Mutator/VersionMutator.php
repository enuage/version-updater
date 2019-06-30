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

use Enuage\VersionUpdaterBundle\Collection\VersionModifierCollection;
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Enuage\VersionUpdaterBundle\ValueObject\VersionComponent;
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
            $this->updatePreRelease($this->options->getPreReleaseModifiers());
        } else {
            $this->version->clearPreRelease();
        }

        $this->version->setMetaComponents($this->options->getMetaComponents());

        return $this;
    }

    /**
     * @param string $type
     */
    private function updateMainVersion(string $type)
    {
        $mainModifier = $this->options->getMainModifier($type);
        $value = $this->version->getMainVersion($type) + ($mainModifier ? $mainModifier->getValue() : 0);

        if ($value < 0) {
            $value = 0;
        }

        if (0 === $value && Version::MAJOR === $type) {
            $this->version->setMinor(1);
        }

        $this->version->setMainVersion($type, $value);
    }

    /**
     * @param VersionModifierCollection $modifiers
     */
    private function updatePreRelease(VersionModifierCollection $modifiers)
    {
        $definedType = $this->version->getPreRelease();

        $version = null;
        if ($definedType && $preReleaseComponent = $this->version->getPreReleaseComponent($definedType)) {
            $version = $preReleaseComponent->getValue();
        }
        $isVersionDefined = null !== $version && is_numeric($version);

        /** @var VersionModifier $modifier */
        foreach ($modifiers as $type => $modifier) {
            $isApplicable = $definedType === $type;

            if ($modifier->isEnabled()) {
                $this->version
                    ->clearPreRelease()
                    ->enablePreRelease($type);

                if (!$isApplicable && $this->options->isDowngrade()) {
                    $this->version->clearPreRelease();
                }

                if ($isApplicable && $isVersionDefined) {
                    $this->version->setPreReleaseVersion($type, $version + $modifier->update()->getValue());
                }
            }
        }

        $modifier = $this->options->getPreReleaseVersionModifier();
        if (null !== $definedType && null !== $version && $definedType === $this->version->getPreRelease()) {
            $this->version->enablePreRelease($definedType);

            $version = ($version ?? 0) + $modifier->getValue();

            /** @var VersionComponent $preRelease */
            $preRelease = $this->version->getPreReleaseComponent($definedType);
            if (null !== $preRelease) {
                $preRelease->setValue($version);
            }

            if ((null === $preRelease || $version < 0) && $modifier->isDowngrade()) {
                $this->version->clearPreRelease();
            }
        }
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
