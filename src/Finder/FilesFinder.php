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
use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
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
     */
    public function iterate(Closure $closure)
    {
        foreach ($this->files as $filePath => $pattern) {
            $file = $this->getFile($filePath);

            $closure($file, $pattern);
        }
    }

    /**
     * @param string $path
     *
     * @return SplFileInfo
     *
     * @throws FileNotFoundException
     */
    private function getFile(string $path): SplFileInfo
    {
        $pathToFile = (new StringType($path))->explode(DIRECTORY_SEPARATOR);

        $fileName = new StringType($pathToFile->last());
        $pathToFile->remove($pathToFile->count() - 1);

        $absolutePath = new ArrayCollection([$this->rootDirectory, '..']);
        $absolutePath->append($pathToFile);

        $directory = $absolutePath->implode(DIRECTORY_SEPARATOR)->append(DIRECTORY_SEPARATOR);

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
     */
    private function findFile(StringType $directory, StringType $name, string $fileExtension = null): SplFileInfo
    {
        if (null !== $fileExtension) {
            $name->reset()->append('.')->append($fileExtension);
        }

        $finder = new Finder();
        $finder->files()->in($directory)->name($name);

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
}
