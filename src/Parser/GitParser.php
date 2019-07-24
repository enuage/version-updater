<?php

namespace Enuage\VersionUpdaterBundle\Parser;

/**
 * Class GitParser
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class GitParser
{
    /**
     * @return string
     */
    public function getLatestTag(): string
    {
        exec('git tag 2>&1', $output, $exitCode);

        return end($output);
    }
}
