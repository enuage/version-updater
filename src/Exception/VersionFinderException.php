<?php

namespace Enuage\VersionUpdaterBundle\Exception;

use Exception;

/**
 * Class VersionFinderException
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class VersionFinderException extends Exception implements EnuageExceptionInterface
{
    /**
     * @return VersionFinderException
     */
    public static function composerNotFound(): VersionFinderException
    {
        return new self('No composer file found in this directory.', 404);
    }

    /**
     * @return VersionFinderException
     */
    public static function gitNotFound(): VersionFinderException
    {
        return new self('Git is not initialised in this directory.', 404);
    }
}
