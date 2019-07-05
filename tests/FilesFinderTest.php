<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * FilesFinderTest
 *
 * Created at 2019-07-05 11:49 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Tests;

use Enuage\VersionUpdaterBundle\Exception\FileNotFoundException;
use Enuage\VersionUpdaterBundle\Finder\FilesFinder;
use Enuage\VersionUpdaterBundle\Handler\JsonHandler;
use Enuage\VersionUpdaterBundle\Handler\YamlHandler;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FilesFinderTest
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FilesFinderTest extends FunctionalTestCase
{
    public function testFindTextFiles()
    {
        $finder = new FilesFinder();
        $finder->setRootDirectory($this->getRootDir());
        $finder->setFiles(
            [
                ['support/file.txt' => '/^(version=)\V/m'],
            ]
        );

        $hasFiles = $finder->hasFiles();
        $this->assertTrue($hasFiles);

        $finder->iterate(
            function ($file) {
                $this->assertInstanceOf(SplFileInfo::class, $file);
            }
        );
    }

    /**
     * @return mixed
     */
    private function getRootDir()
    {
        return $this->getKernel()->getContainer()->getParameter('kernel.root_dir');
    }

    public function testFindJsonFiles()
    {
        $finder = new FilesFinder();
        $finder->setRootDirectory($this->getRootDir());
        $finder->setExtensions(JsonHandler::getExtensions());
        $finder->setFiles(
            [
                ['support/composer' => 'version'],
                ['support/doc/api' => 'info/version'],
            ]
        );

        $hasFiles = $finder->hasFiles();
        $this->assertTrue($hasFiles);

        $finder->iterate(
            function ($file) {
                $this->assertInstanceOf(SplFileInfo::class, $file);
            }
        );
    }

    public function testFindYamlFiles()
    {
        $finder = new FilesFinder();
        $finder->setRootDirectory($this->getRootDir());
        $finder->setExtensions(YamlHandler::getExtensions());
        $finder->setFiles(
            [
                ['support/doc/api' => 'info/version'],
            ]
        );

        $hasFiles = $finder->hasFiles();
        $this->assertTrue($hasFiles);

        $finder->iterate(
            function ($file) {
                $this->assertInstanceOf(SplFileInfo::class, $file);
            }
        );
    }

    public function testFileNotFound()
    {
        $finder = new FilesFinder();
        $finder->setRootDirectory($this->getRootDir());
        $finder->setExtensions(YamlHandler::getExtensions());
        $finder->setFiles(
            [
                ['support/undefined' => 'version'],
            ]
        );

        $this->expectException(FileNotFoundException::class);
        $finder->iterate(
            function ($file, $pattern) {
                // Will fail anyway
            }
        );
    }
}
