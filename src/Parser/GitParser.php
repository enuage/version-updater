<?php

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\Command\GitCommand;
use Enuage\VersionUpdaterBundle\Exception\VersionFinderException;

/**
 * Class GitParser
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class GitParser
{
    /**
     * @throws VersionFinderException
     */
    public function check(): void
    {
        if (!is_dir(getcwd().'/.git')) {
            throw VersionFinderException::gitNotFound();
        }
    }

    /**
     * @return string
     */
    public function getLatestTag(): string
    {
        $tags = GitCommand::run('tag', true);

        return end($tags);
    }
}
