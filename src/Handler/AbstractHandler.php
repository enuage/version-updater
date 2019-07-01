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

use Closure;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
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
     * @var string
     */
    protected $pattern;

    /**
     * @param FileParser $parser
     * @param FormatterInterface $formatter
     *
     * @return string
     */
    abstract public function handle(FileParser $parser, FormatterInterface $formatter): string;

    /**
     * @param FileParser $parser
     *
     * @return string
     */
    abstract public function getFileContent(FileParser $parser): string;

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
    public function setPattern(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param array $content
     * @param array $properties
     * @param Closure $closure
     */
    protected function accessProperty(array &$content, array $properties, Closure $closure)
    {
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
