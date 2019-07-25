<?php
/**
 * FilesFinder
 *
 * Created at 2019-06-23 1:56 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Finder;

use Closure;
use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Enuage\VersionUpdaterBundle\DependencyInjection\Configuration;
use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
use Enuage\VersionUpdaterBundle\Exception\InvalidFileException;
use Enuage\VersionUpdaterBundle\Helper\Type\StringType;
use Enuage\VersionUpdaterBundle\Normalizer\FilesArrayNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FilesFinder
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FilesFinder
{
    /**
     * @var ArrayCollection
     */
    private $files;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var ArrayCollection
     */
    private $extensions;

    /**
     * FilesFinder constructor.
     */
    public function __construct()
    {
        $this->rootDirectory = getcwd();
        $this->extensions = new ArrayCollection();
    }

    /**
     * @param array $files
     *
     * @return FilesFinder
     */
    public function setFiles(array $files): FilesFinder
    {
        $this->files = FilesArrayNormalizer::normalize($files);

        return $this;
    }

    /**
     * @param Closure $closure
     *
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    public function iterate(Closure $closure): void
    {
        foreach ($this->files as $filePath => $pattern) {
            $file = $this->getFile($filePath);

            $closure($file, $pattern);
        }
    }

    /**
     * @param string $path
     * @param bool $isConfiguration
     *
     * @return SplFileInfo
     *
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    public function getFile(string $path, bool $isConfiguration = false): SplFileInfo
    {
        $pathToFile = new StringType($path);

        $absolutePath = new ArrayCollection([$this->rootDirectory]);
        $absolutePath->append($pathToFile);

        $isPathFromRoot = $this->isFromRoot($pathToFile);
        $pathToFile = $pathToFile->explode(DIRECTORY_SEPARATOR, StringType::REMOVE_EMPTY_ELEMENTS);

        if ($isPathFromRoot) {
            $absolutePath = $pathToFile;
        }

        $fileName = new StringType($isConfiguration ? Configuration::CONFIG_FILE : $pathToFile->last());
        if (!$isConfiguration || ($isConfiguration && Configuration::CONFIG_FILE === $pathToFile->last())) {
            $pathToFile->removeElement($pathToFile->last());
        }

        $directory = ($isPathFromRoot ? $absolutePath : $pathToFile)->implode(DIRECTORY_SEPARATOR);

        if (!$directory->endsWith(DIRECTORY_SEPARATOR)) {
            $directory->append(DIRECTORY_SEPARATOR);
        }

        if ($isPathFromRoot && !$directory->startsWith(DIRECTORY_SEPARATOR)) {
            $directory->prepend(DIRECTORY_SEPARATOR);
        }

        if ($directory->isEqualTo(DIRECTORY_SEPARATOR)) {
            $directory = new StringType('.');
        }

        return $this->findFile($directory, $fileName);
    }

    /**
     * @param StringType $directory
     * @param StringType $name
     * @param string|null $fileExtension
     *
     * @return SplFileInfo
     *
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    private function findFile(StringType $directory, StringType $name, string $fileExtension = null): SplFileInfo
    {
        if ($name->isEmpty()) {
            throw new InvalidFileException($directory, $name);
        }

        if (null !== $fileExtension) {
            $name->reset()->append('.')->append($fileExtension);
        }

        if (!$directory->startsWith('.')) {
            $directory->regexPrepare();
        }

        $finder = new Finder();
        $finder->files();
        $finder->in($directory->getValue());
        $finder->notPath('vendor');
        $finder->depth(0); // Restrict recursive search
        $finder->name($name->getValue());
        $finder->ignoreDotFiles(false);

        $file = ArrayCollection::fromIterator($finder->getIterator())->first();

        if (is_bool($file) && !$this->extensions->isEmpty()) {
            $extension = $this->extensions->current();

            if (null !== $fileExtension) {
                $extension = $this->extensions->getNext($fileExtension);
            }

            if (null === $extension) {
                throw new FileNotFoundException($directory, $name->getInitialValue(), $this->extensions);
            }

            return $this->findFile($directory, $name, $extension);
        }

        if (!$finder->hasResults()) {
            throw new FileNotFoundException($directory, $name->getInitialValue(), $this->extensions);
        }

        return $file;
    }

    /**
     * @param string $rootDirectory
     *
     * @return FilesFinder
     */
    public function setRootDirectory(string $rootDirectory): FilesFinder
    {
        $this->rootDirectory = $rootDirectory;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFiles(): bool
    {
        return !$this->files->isEmpty();
    }

    /**
     * @param array $extensions
     *
     * @return FilesFinder
     */
    public function setExtensions(array $extensions): FilesFinder
    {
        $this->extensions = new ArrayCollection($extensions);

        return $this;
    }

    /**
     * @param StringType $path
     *
     * @return bool
     */
    private function isFromRoot(StringType $path): bool
    {
        return $path->startsWith(DIRECTORY_SEPARATOR) || $path->startsWith('~/');
    }
}
