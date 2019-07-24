<?php
/**
 * UpdatingService
 *
 * Created at 2019-06-23 3:56 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Service;

use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Exception\EnuageExceptionInterface;
use Enuage\VersionUpdaterBundle\Finder\FilesFinder;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\Handler\AbstractHandler;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Parser\FileParser;
use Enuage\VersionUpdaterBundle\Parser\VersionParser;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;

/**
 * Class VersionService
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionService
{
    /**
     * @param string|Version $version
     * @param VersionOptions $options
     *
     * @return VersionMutator
     *
     * @throws Exception
     */
    public function update($version, VersionOptions $options): VersionMutator
    {
        if (!($version instanceof Version)) {
            $versionParser = new VersionParser($version);
            $version = $versionParser->parse();
        }

        $versionMutator = new VersionMutator($version, $options);

        return $versionMutator->update();
    }

    /**
     * @param string $filePath
     * @param string $type
     *
     * @return string
     *
     * @throws EnuageExceptionInterface
     */
    public function getVersionFromFile(string $filePath, string $type)
    {
        $finder = new FilesFinder();
        $file = $finder->getFile($filePath, false);

        $parser = new FileParser($file, AbstractHandler::getHandlerByFileType($type));

        $formatter = new VersionFormatter();

        return $formatter->setVersion($parser->parse())->format();
    }
}
