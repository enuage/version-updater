<?php

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigurationParser
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class ConfigurationParser
{
    /**
     * @var ArrayCollection
     */
    private $configurations;

    /**
     * ConfigurationParser constructor.
     *
     * @param array $configurations
     */
    public function __construct(array $configurations = [])
    {
        $this->configurations = new ArrayCollection($configurations);
    }

    /**
     * @param array $parameters
     *
     * @return ConfigurationParser
     */
    public static function parseConfiguration(array $parameters)
    {
        return new self($parameters);
    }

    /**
     * @param string $content
     *
     * @return ConfigurationParser
     */
    public static function parseFile(string $content)
    {
        return new self(Yaml::parse($content));
    }

    /**
     * @param string $type
     *
     * @return array|null
     */
    public function getFiles(string $type = 'files')
    {
        return $this->configurations->containsKey($type) ? $this->configurations->get($type) : null;
    }
}
