<?php

namespace Enuage\VersionUpdaterBundle\Finder;

use Doctrine\Common\Collections\Collection;
use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\Exception\VersionFinderException;
use Enuage\VersionUpdaterBundle\Helper\Type\FileType;
use Enuage\VersionUpdaterBundle\Parser\GitParser;
use Enuage\VersionUpdaterBundle\Service\VersionService;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class VersionFinder
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class VersionFinder
{
    const SOURCE_COMPOSER = 'composer';
    const SOURCE_GIT = 'git';

    /**
     * @var Collection
     */
    private $sources;

    /**
     * @var VersionService
     */
    private $versionService;

    /**
     * VersionFinder constructor.
     */
    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->versionService = new VersionService();
    }

    /**
     * @return $this
     *
     * @throws VersionFinderException
     */
    public function findAll()
    {
        $this->getComposerVersion(true);
        $this->getGitVersion(true);

        return $this;
    }

    /**
     * @param bool $silent
     *
     * @return string
     *
     * @throws VersionFinderException
     */
    public function getComposerVersion(bool $silent = false): string
    {
        $filePath = getcwd().'/composer.json';

        if (!file_exists($filePath)) {
            if (false === $silent) {
                throw VersionFinderException::composerNotFound();
            }

            $version = 'No data';
        } else {
            $version = $this->versionService->getVersionFromFile($filePath, FileType::TYPE_JSON_COMPOSER);

            $this->addSource(self::SOURCE_COMPOSER, $version);
        }

        return $version;
    }

    /**
     * @param string $type
     * @param string $version
     */
    private function addSource(string $type, string $version)
    {
        $this->sources->add(new ArrayCollection(['type' => $type, 'version' => $version]));
    }

    /**
     * @param bool $silent
     *
     * @return string
     *
     * @throws VersionFinderException
     */
    public function getGitVersion(bool $silent = false): string
    {
        try {
            $gitParser = new GitParser();
            $gitParser->check();

            $version = $this->versionService->getVersionFromGit();

            $this->addSource(self::SOURCE_GIT, $version);
        } catch (VersionFinderException $exception) {
            if (false === $silent) {
                throw VersionFinderException::gitNotFound();
            }

            $version = 'No data';
        }

        return $version;
    }

    /**
     * @param SymfonyStyle $io
     */
    public function cliOutput(SymfonyStyle $io)
    {
        if (!$this->sources->isEmpty()) {
            /** @var Collection $source */
            foreach ($this->sources->getIterator() as $source) {
                $io->writeln($source->get('type').': '.$source->get('version'));
            }
        }
    }
}
