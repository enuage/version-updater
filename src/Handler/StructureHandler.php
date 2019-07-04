<?php

namespace Enuage\VersionUpdaterBundle\Handler;

use Closure;
use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;

/**
 * Class StructureHandler
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
abstract class StructureHandler extends AbstractHandler
{
    /**
     * @param array $content
     * @param FormatterInterface $formatter
     */
    protected function updateProperty(array &$content, FormatterInterface $formatter)
    {
        $this->accessProperty(
            $content,
            static function (&$property) use ($formatter) {
                $property = $formatter->format();
            }
        );
    }

    /**
     * @param array $content
     * @param Closure $closure
     * @param ArrayCollection $properties
     */
    private function accessProperty(array &$content, Closure $closure, ArrayCollection $properties = null)
    {
        if (null === $properties) {
            $properties = $this->pattern->explode('/');
        }

        foreach ($properties->getIterator() as $index => $property) {
            if (array_key_exists($property, $content)) {
                $propertyValue = &$content[$property];

                if (is_array($propertyValue)) {
                    $properties->remove($index);

                    $this->accessProperty($propertyValue, $closure, $properties);
                }

                if (is_string($propertyValue)) {
                    $closure($propertyValue);
                }
            }
        }
    }

    /**
     * @param array $content
     *
     * @return mixed
     */
    private function getValue(array $content)
    {
        $this->accessProperty(
            $content,
            static function ($property) use (&$value) {
                $value = $property;
            }
        );

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getFileContent(): string
    {
        $content = $this->decodeContent();

        return $this->getValue($content);
    }

    /**
     * @return array
     */
    abstract protected function decodeContent(): array;
}
