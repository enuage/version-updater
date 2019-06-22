<?php
/**
 * FileFormatter
 *
 * Created at 2019-06-23 12:55 AM
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

use Enuage\VersionUpdaterBundle\Parser\FileParser;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FileFormatter
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FileFormatter implements FormatterInterface
{
    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * FileFormatter constructor.
     *
     * @param FileParser $fileParser
     */
    public function __construct(FileParser $fileParser)
    {
        $this->fileParser = $fileParser;

        $this->fileSystem = new Filesystem();
    }

    /**
     * @param VersionFormatter $versionFormatter
     *
     * @return bool
     *
     * @throws Exception
     */
    public function format($versionFormatter): bool
    {
        $fileParser = $this->getFileParser();

        $matches = $fileParser->getMatches();
        $lastMatch = $matches->last();
        $lastMatchValue = !is_numeric($lastMatch) && $matches->count() > 12 ? $lastMatch : '';

        $file = $fileParser->getFile();

        $content = preg_replace(
            $fileParser->getPattern(),
            sprintf('${1}%s%s', $versionFormatter->format(), $lastMatchValue),
            $file->getContents()
        );

        $this->fileSystem->dumpFile($file->getRealPath(), $content);

        return true;
    }

    /**
     * @return FileParser
     */
    private function getFileParser(): FileParser
    {
        return $this->fileParser;
    }
}
