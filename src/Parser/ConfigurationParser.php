<?php

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
use Enuage\VersionUpdaterBundle\Finder\FilesFinder;
use Enuage\VersionUpdaterBundle\Handler\YamlHandler;

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
     * @param string $path
     *
     * @return ConfigurationParser
     *
     * @throws FileNotFoundException
     */
    public static function parseFile(string $path)
    {
        $fileParser = new FileParser(FilesFinder::getFileFromPath($path), new YamlHandler());

        return new self($fileParser->decodeContent());
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
