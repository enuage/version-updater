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

    /**
     * @param string $message
     * @param bool $all
     *
     * @return void
     */
    public static function commit(string $message, bool $all = false): void
    {
        $options = [];

        if (true === $all) {
            $options[] = '-a';
        }

        $options[] = '-m';
        $options[] = '"'.$message.'"';

        self::run(implode(' ', $options));
    }

    /**
     * @return void
     */
    public static function push(): void
    {
        self::run('push');
    }

    /**
     * @param string $tag
     * @param string $message
     *
     * @return void
     */
    public static function createTag(string $tag, string $message): void
    {
        self::run(sprintf('tag -a %s -m "%s"', $tag, $message));
    }

    /**
     * @param string $tag
     *
     * @return void
     */
    public static function pushTag(string $tag): void
    {
        self::run(sprintf('push -u origin %s', $tag));
    }

    /**
     * @param array $files
     *
     * @return void
     */
    public static function addFiles(array $files): void
    {
        self::run(sprintf('add %s', implode(' ', $files)));
    }

    /**
     * @return void
     */
    public static function addAllFiles(): void
    {
        self::addFiles(['.']);
    }
}
