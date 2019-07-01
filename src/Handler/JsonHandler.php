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

use Closure;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Parser\AbstractParser;
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
     * @param array $content
     * @param array $properties
     * @param Closure $closure
     */
    private function accessProperty(array &$content, array $properties, Closure $closure)
    {
        if (JSON_ERROR_NONE === json_last_error()) {
            foreach ($properties as $index => $property) {
                if (array_key_exists($property, $content)) {
                    $propertyValue = &$content[$property];

                    if (is_array($propertyValue)) {
                        unset($properties[$index]);

                        $this->accessProperty($propertyValue, $properties, $closure);
                    }

                    if (is_string($propertyValue)) {
                        $closure($propertyValue);
                    }
                }
            }
        }
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

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        return sprintf('/%s/', AbstractParser::VERSION_PATTERN);
    }
}
