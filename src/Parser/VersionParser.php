<?php
/**
 * VersionParser
 *
 * Created at 2019-06-23 12:24 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\ValueObject\Version;

/**
 * Class VersionParser
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionParser extends AbstractParser
{
    /**
     * @var string
     */
    private $subject;

    /**
     * VersionParser constructor.
     *
     * @param string $subject
     */
    public function __construct(string $subject = null)
    {
        parent::__construct();

        $this->subject = $subject;

        $this->setPattern(sprintf('/%s/', self::VERSION_PATTERN));
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): Version
    {
        preg_match($this->getPattern(), $this->getSubject(), $matches);

        $this->cloneMatches($matches);

        return $this->getVersion();
    }

    /**
     * @return string
     */
    private function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return Version
     */
    private function getVersion(): Version
    {
        $version = new Version();

        if ($this->matches->containsKey('prefix')) {
            $version->setPrefix($this->matches->get('prefix'));
        }

        if ($this->matches->containsKey('majorVersion')) {
            $version->setMajor($this->matches->getIntValue('majorVersion', 0));
        }

        if ($this->matches->containsKey('minorVersion')) {
            $version->setMinor($this->matches->getIntValue('minorVersion', 0));
        }

        if ($this->matches->containsKey('patchVersion')) {
            $version->setPatch($this->matches->getIntValue('patchVersion', 0));
        }

        if ($this->matches->containsKey('preRelease')) {
            $type = $this->matches->get('preRelease');
            $version->enablePreRelease($type);

            if ($this->matches->containsKey('preReleaseVersion')) {
                $version->getPreReleaseComponent($type)->setValue(
                    $this->matches->getIntValue('preReleaseVersion', 0)
                );
            }
        }

        return $version;
    }
}
