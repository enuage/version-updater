<?php

namespace Enuage\VersionUpdaterBundle\Handler;

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
}
