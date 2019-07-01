<?php

namespace Enuage\VersionUpdaterBundle\Handler;

use Closure;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Parser\FileParser;

/**
 * Class FormatHandler
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
abstract class FormatHandler extends AbstractHandler
{
    /**
     * @var FileParser
     */
    private $parser;

    /**
     * @return FileParser
     */
    protected function getParser(): FileParser
    {
        return $this->parser;
    }

    /**
     * @param FileParser $parser
     */
    protected function setParser(FileParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param array $content
     * @param FormatterInterface $formatter
     */
    protected function updateProperty(array &$content, FormatterInterface $formatter)
    {
        $this->accessProperty(
            $content,
            $this->getProperties(),
            static function (&$property) use ($formatter) {
                $property = $formatter->format();
            }
        );
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

    /**
     * @return array
     */
    protected function getProperties(): array
    {
        return explode('/', $this->pattern);
    }

    /**
     * @param array $content
     *
     * @return mixed
     */
    protected function getValue(array $content)
    {
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
