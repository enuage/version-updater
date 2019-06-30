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

use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Enuage\VersionUpdaterBundle\ValueObject\VersionComponent;

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
        $result = $this->version->getPrefix() ?? '';

        $mainVersionValues = [];
        /** @var VersionComponent $component */
        foreach ($this->version->getMainComponents()->getIterator() as $component) {
            $mainVersionValues[] = $component->getValue();
        }

        $result .= implode('.', $mainVersionValues);

        if ($preRelease = $this->version->getPreRelease()) {
            $result .= '-'.$preRelease;

            if ($preReleaseVersion = $this->version->getPreReleaseVersion()) {
                $result .= '.'.$preReleaseVersion;
            }
        }

        if ($this->version->isDateMetaDefined()) {
            $dateFormat = $this->version->getDateMetaFormat();

            $result .= '+'.$this->version->getDateMetaValue()->format($dateFormat);
        }

        if ($meta = $this->version->getMetaValue()) {
            $result .= '+'.$meta;
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
