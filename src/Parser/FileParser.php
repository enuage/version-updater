<?php
/**
 * FileParser
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

use Enuage\VersionUpdaterBundle\Handler\AbstractHandler;
use Enuage\VersionUpdaterBundle\Handler\StructureHandler;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileParser
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FileParser extends AbstractParser
{
    public const FILE_VERSION_PATTERN = '\V';

    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @var AbstractHandler
     */
    private $handler;

    /**
     * FileParser constructor.
     *
     * @param SplFileInfo $file
     * @param AbstractHandler $handler
     */
    public function __construct(SplFileInfo $file, AbstractHandler $handler)
    {
        parent::__construct();

        $this->file = $file;
        $this->handler = $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): Version
    {
        $versionParser = new VersionParser($this->getFileContent());
        $versionParser->setPattern($this->handler->getPattern());

        $this->cloneMatches($versionParser->getMatches());

        return $versionParser->parse();
    }

    /**
     * @return mixed
     */
    public function getFileContent()
    {
        return $this->handler->setParser($this)->getFileContent();
    }

    /**
     * @return array|string
     */
    public function decodeContent()
    {
        if ($this->handler instanceof StructureHandler) {
            return $this->handler->decodeContent($this->getFile()->getContents());
        }

        return $this->getFileContent();
    }

    /**
     * @return SplFileInfo
     */
    public function getFile(): SplFileInfo
    {
        return $this->file;
    }
}
