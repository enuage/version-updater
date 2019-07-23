<?php

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
use Enuage\VersionUpdaterBundle\Exception\InvalidFileException;
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
    public static function parseConfiguration(array $parameters): ConfigurationParser
    {
        return new self($parameters);
    }

    /**
     * @param string $path
     * @param FilesFinder|null $finder
     *
     * @return ConfigurationParser
     *
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    public static function parseFile(string $path, FilesFinder $finder = null): ConfigurationParser
    {
        if(null === $finder) {
            $finder = new FilesFinder();
        }

        $fileParser = new FileParser($finder->getFile($path, true), new YamlHandler());

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
