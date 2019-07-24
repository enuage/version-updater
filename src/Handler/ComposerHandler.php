<?php

namespace Enuage\VersionUpdaterBundle\Handler;

use Enuage\VersionUpdaterBundle\Exception\InvalidFileException;
use Exception;

/**
 * Class ComposerHandler
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
final class ComposerHandler extends JsonHandler
{
    const VERSION_PROPERTY = "version"; // https://getcomposer.org/doc/04-schema.md#version

    /**
     * ComposerHandler constructor.
     */
    public function __construct() {
        $this->setPattern(self::VERSION_PROPERTY);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function decodeContent(string $content): array
    {
        $result = parent::decodeContent($content);

        if (
            empty($result)
            || !array_key_exists(self::VERSION_PROPERTY, $result)
            || empty($result[self::VERSION_PROPERTY])
        ) {
            $file = $this->getParser()->getFile();
            throw InvalidFileException::versionNotFound($file->getFilename());
        }

        return $result;
    }
}
