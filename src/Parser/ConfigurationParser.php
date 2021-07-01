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
    protected const GET_MESSAGE_COMMIT = 'commit';
    protected const GET_MESSAGE_RELEASE = 'release';

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
    public function getFiles(string $type = 'files'): ?array
    {
        return $this->configurations->containsKey($type) ? $this->configurations->get($type) : null;
    }

    /**
     * @return bool
     */
    public function isGitEnabled(): bool
    {
        return $this->configurations->containsKey('git');
    }

    /**
     * @return bool
     */
    public function isGitPushEnabled(): bool
    {
        if ($this->isGitEnabled()) {
            $gitConfiguration = $this->getGitConfiguration();

            if (array_key_exists('push', $gitConfiguration)) {
                return filter_var($gitConfiguration['push'], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getGitPrefix(): string
    {
        if ($this->isGitEnabled()) {
            $gitConfiguration = $this->getGitConfiguration();

            if (array_key_exists('prefix', $gitConfiguration)) {
                return (string) $gitConfiguration['prefix'];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    private function getGitConfiguration(): array
    {
        return $this->configurations->getValue('git', []);
    }

    /**
     * @param string $defaultMessage
     *
     * @return string
     */
    public function getGitCommitMessage(string $defaultMessage): string
    {
        return $this->getGitMessage(self::GET_MESSAGE_COMMIT, $defaultMessage);
    }

    /**
     * @param string $defaultMessage
     *
     * @return string
     */
    public function getGitReleaseMessage(string $defaultMessage): string
    {
        return $this->getGitMessage(self::GET_MESSAGE_RELEASE, $defaultMessage);
    }

    protected function getGitMessage(
        string $type,
        string $defaultMessage
    ): string {
        if ($this->isGitEnabled()) {
            $gitConfiguration = $this->getGitConfiguration();

            $messageConfiguration = [];
            if (array_key_exists('message', $gitConfiguration)) {
                $messageConfiguration = $gitConfiguration['message'] ?? [];
            }

            if (array_key_exists($type, $messageConfiguration)) {
                if (!empty($messageConfiguration[$type])) {
                    return $messageConfiguration[$type];
                }
            }
        }

        return $defaultMessage;
    }
}
