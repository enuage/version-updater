<?php
/**
 * JsonHandler
 *
 * Created at 2019-06-30 11:46 PM
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
use Enuage\VersionUpdaterBundle\Parser\FileParser;

/**
 * Class JsonHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class JsonHandler extends AbstractHandler
{
    /**
     * @var FileParser
     */
    private $parser;

    /**
     * {@inheritDoc}
     */
    public function handle(FileParser $parser, FormatterInterface $formatter): string
    {
        $this->parser = $parser;
        $content = $this->decodeContent();

        $this->accessProperty(
            $content,
            $this->getProperties(),
            static function (&$property) use ($formatter) {
                $property = $formatter->format();
            }
        );

        $content = json_encode($content, JSON_PRETTY_PRINT).PHP_EOL;

        return $content;
    }

    /**
     * @return mixed
     */
    private function decodeContent()
    {
        return json_decode($this->parser->getFile()->getContents(), true);
    }

    /**
     * @return array
     */
    private function getProperties(): array
    {
        return explode('/', $this->pattern);
    }

    /**
     * @param FileParser $parser
     *
     * @return string
     */
    public function getFileContent(FileParser $parser): string
    {
        $this->parser = $parser;
        $content = $this->decodeContent();

        $this->accessProperty(
            $content,
            $this->getProperties(),
            static function ($property) use (&$value) {
                $value = $property;
            }
        );

        return $value;
    }
}
