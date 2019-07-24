<?php
/**
 * AbstractHandler
 *
 * Created at 2019-06-30 11:39 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Handler;

use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Helper\Type\FileType;
use Enuage\VersionUpdaterBundle\Helper\Type\StringType;
use Enuage\VersionUpdaterBundle\Parser\AbstractParser;
use Enuage\VersionUpdaterBundle\Parser\FileParser;

/**
 * Class AbstractHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
abstract class AbstractHandler
{
    /**
     * @var StringType
     */
    protected $pattern;

    /**
     * @var FileParser
     */
    private $parser;

    /**
     * @param FormatterInterface $formatter
     *
     * @return string
     */
    abstract public function handle(FormatterInterface $formatter): string;

    /**
     * @return string
     */
    public function getFileContent(): string {
        return $this->getParser()->getFile()->getContents();
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return sprintf('/%s/', AbstractParser::VERSION_PATTERN);
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = new StringType($pattern);
    }

    /**
     * @param FileParser $parser
     *
     * @return AbstractHandler
     */
    public function setParser(FileParser $parser): AbstractHandler
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return FileParser
     */
    protected function getParser(): FileParser
    {
        return $this->parser;
    }

    /**
     * @param string $fileType
     *
     * @return AbstractHandler|ComposerHandler
     */
    public static function getHandlerByFileType(string $fileType): AbstractHandler
    {
        switch ($fileType) {
            case FileType::TYPE_JSON:
                return new JsonHandler();
                break;
            case FileType::TYPE_JSON_COMPOSER:
                return new ComposerHandler();
                break;
            case FileType::TYPE_YAML:
                return new YamlHandler();
                break;
            default:
                return new TextHandler();
                break;
        }
    }
}
