<?php

namespace Enuage\VersionUpdaterBundle\Command;

/**
 * Class GitCommand
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class GitCommand
{
    /**
     * @param string $gitCommand
     *
     * @return mixed
     */
    public static function run(string $gitCommand)
    {
        exec(sprintf('git %s 2>&1', $gitCommand), $output, $exitCode);

        return $output;
    }
}
