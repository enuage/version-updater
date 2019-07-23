<?php
/**
 * VersionFormatter
 *
 * Created at 2019-06-22 1:22 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Formatter;

use Enuage\VersionUpdaterBundle\Helper\Type\StringType;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\ValueObject\MetaComponent;
use Enuage\VersionUpdaterBundle\ValueObject\Version;

/**
 * Class VersionFormatter
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionFormatter implements FormatterInterface
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @param Version|VersionMutator $subject
     *
     * @return string
     */
    public function format($subject = null): string
    {
        $result = new StringType($this->version->getPrefix() ?? '');

        $result->append(implode('.', $this->version->getMainComponents()->getValues()));

        if ($preRelease = $this->version->getPreRelease()) {
            $result->append('-')->append($preRelease);

            $preReleaseVersion = $this->version->getPreReleaseComponent($preRelease);
            if ($preReleaseVersion->getValue() > 0) {
                $result->append('.')->append($preReleaseVersion->getValue());
            }
        }

        $metaComponents = $this->version->getMetaComponents();
        if (!$metaComponents->isEmpty()) {
            /** @var MetaComponent $component */
            foreach ($metaComponents->getIterator() as $component) {
                $metaValue = $component->getValue();
                $metaFormat = $component->getFormat();
                if (null !== $metaFormat && MetaComponent::TYPE_DATETIME === $component->getType()) {
                    $metaValue = $metaValue->format($metaFormat) ?? 'c';
                }

                if (is_string($metaValue)) {
                    $result->append('+')->append($metaValue);
                }
            }
        }

        return $result;
    }

    /**
     * @param Version $version
     *
     * @return FormatterInterface
     */
    public function setVersion(Version $version): FormatterInterface
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param VersionMutator $versionMutator
     *
     * @return FormatterInterface
     */
    public function setMutator(VersionMutator $versionMutator): FormatterInterface
    {
        $this->version = $versionMutator->getVersion();

        return $this;
    }
}
