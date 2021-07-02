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
     * @param bool $raw
     *
     * @return mixed|string|null
     */
    public static function run(string $gitCommand, bool $raw = false)
    {
        exec(sprintf('git %s 2>&1', $gitCommand), $output, $exitCode);

        if (true === $raw) {
            return $output;
        }

        if (null === $output) {
            return null;
        }

        if (\is_array($output)) {
            return \implode(PHP_EOL, $output);
        }

        return \strval($output);
    }

    public static function commit(string $message, bool $all = false): ?string
    {
        $options = [];

        if (true === $all) {
            $options[] = '-a';
        }

        $options[] = '-m';
        $options[] = sprintf('"%s"', $message);

        return self::run('commit '.implode(' ', $options));
    }

    public static function pushLatestCommit(): ?string
    {
        return self::run('push -u origin HEAD');
    }

    public static function createTag(string $tag, string $message): ?string
    {
        return self::run(sprintf('tag -a %s -m "%s"', $tag, $message));
    }

    public static function pushTag(string $tag): ?string
    {
        return self::run(sprintf('push -u origin %s', $tag));
    }

    public static function addFiles(array $files): ?string
    {
        return self::run(sprintf('add %s', implode(' ', $files)));
    }

    public static function addAllFiles(): ?string
    {
        return self::addFiles(['.']);
    }
}
