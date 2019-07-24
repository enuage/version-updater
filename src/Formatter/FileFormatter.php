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

use Enuage\VersionUpdaterBundle\Handler\AbstractHandler;
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
     * @var AbstractHandler
     */
    private $handler;

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
     * @param FormatterInterface|VersionFormatter $versionFormatter
     *
     * @return string
     *
     * @throws Exception
     */
    public function format($versionFormatter = null): string
    {
        $fileParser = $this->fileParser;
        $file = $fileParser->getFile();

        $this->fileSystem->dumpFile(
            $file->getRealPath(),
            $this->handler->setParser($fileParser)->handle($versionFormatter)
        );

        return $versionFormatter->format();
    }

    /**
     * @param AbstractHandler $handler
     */
    public function setHandler(AbstractHandler $handler): void
    {
        $this->handler = $handler;
    }
}
