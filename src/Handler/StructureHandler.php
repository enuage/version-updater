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
     * @return array
     */
    abstract public static function getExtensions(): array;

    /**
     * {@inheritDoc}
     */
    public function getFileContent(): string
    {
        return $this->getValue($this->decodeContent(parent::getFileContent()));
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
     * @param FormatterInterface $formatter
     *
     * @return array
     */
    protected function updateProperty(FormatterInterface $formatter): array
    {
        $content = $this->decodeContent(parent::getFileContent());

        $this->accessProperty(
            $content,
            static function (&$property) use ($formatter) {
                $property = $formatter->format();
            }
        );

        return $content;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    abstract public function decodeContent(string $content): array;

    /**
     * @param array $content
     * @param Closure $closure
     * @param ArrayCollection $properties
     */
    private function accessProperty(array &$content, Closure $closure, ArrayCollection $properties = null): void
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
}
