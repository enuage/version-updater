<?php
/**
 * AbstractParser
 *
 * Created at 2019-06-23 12:40 AM
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

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\ValueObject\Version;

/**
 * Class AbstractParser
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
abstract class AbstractParser
{
    const VERSION_PATTERN = '(?>'.
    '(?<prefix>[a-zA-Z]+)?'.
    '(?<majorVersion>\d+)\.?'.
    '(?<minorVersion>\d*)\.?'.
    '(?<patchVersion>\d*)'.
    '(?>\-(?<preRelease>alpha|beta|rc)'.
    '(?>\.(?<preReleaseVersion>\d+))?)?'.
    '(?>\+[a-zA-Z\d]+)*'. // Metadata isn't captured
    ')';

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var ArrayCollection
     */
    protected $matches;

    /**
     * AbstractParser constructor.
     */
    public function __construct()
    {
        $this->matches = new ArrayCollection();
    }

    /**
     * @return Version
     *
     * @return mixed
     */
    abstract public function parse(): Version;

    /**
     * @return ArrayCollection
     */
    public function getMatches(): ArrayCollection
    {
        return $this->matches;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     *
     * @return AbstractParser
     */
    abstract public function setPattern(string $pattern): AbstractParser;

    /**
     * @param array|ArrayCollection $matches
     *
     * @return AbstractParser
     */
    protected function cloneMatches($matches): AbstractParser
    {
        if (is_array($matches)) {
            $this->matches = new ArrayCollection($matches);
        }

        if ($matches instanceof ArrayCollection) {
            $this->matches = $matches;
        }

        return $this;
    }
}
