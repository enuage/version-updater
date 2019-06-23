<?php
/**
 * TextParser
 *
 * Created at 2019-06-23 12:30 AM
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
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class TextParser
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FileParser extends AbstractParser
{
    const FILE_VERSION_PATTERN = '\V';

    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * TextParser constructor.
     *
     * @param SplFileInfo $file
     * @param string $pattern
     */
    public function __construct(SplFileInfo $file, string $pattern)
    {
        parent::__construct();

        $this->file = $file;

        $this->setPattern($pattern);
    }

    /**
     * {@inheritDoc}
     */
    public function setPattern(string $pattern): AbstractParser
    {
        $this->pattern = str_replace(self::FILE_VERSION_PATTERN, self::VERSION_PATTERN, $pattern);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): Version
    {
        $versionParser = new VersionParser($this->getFile()->getContents());
        $versionParser->setPattern($this->getPattern());

        $this->cloneMatches($versionParser->getMatches());

        return $versionParser->parse();
    }

    /**
     * @return SplFileInfo
     */
    public function getFile(): SplFileInfo
    {
        return $this->file;
    }
}
