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
     * @param array $files
     */
    public function setFiles(array $files)
    {
        $this->files = FilesArrayNormalizer::normalize($files);
    }

    /**
     * @param Closure $closure
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
     */
    private function getFile(string $path): SplFileInfo
    {
        $filePath = explode('/', $path);

        $lastIndex = count($filePath) - 1;
        $fileName = $filePath[$lastIndex];
        unset($filePath[$lastIndex]);

        $filePath = array_merge([$this->rootDirectory, '..'], $filePath);
        $filePath = implode('/', $filePath).'/';

        $finder = new Finder();
        $finder->files()->in($filePath)->name($fileName);

        return array_values(iterator_to_array($finder->getIterator()))[0];
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
}
