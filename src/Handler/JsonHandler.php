<?php
/**
 * JsonHandler
 *
 * Created at 2019-06-30 11:46 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Handler;

use Enuage\VersionUpdaterBundle\Exception\InvalidFileException;
use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;

/**
 * Class JsonHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class JsonHandler extends StructureHandler
{
    /**
     * {@inheritDoc}
     */
    public static function getExtensions(): array
    {
        return [
            'json',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function handle(FormatterInterface $formatter): string
    {
        $content = json_encode($this->updateProperty($formatter), JSON_PRETTY_PRINT).PHP_EOL;
        $content = str_replace('\/', '/', $content);

        return $content;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidFileException
     */
    public function decodeContent(string $content): array
    {
        $result = json_decode($content, true);

        if (null === $result) {
            $file = $this->getParser()->getFile();
            throw new InvalidFileException('', $file->getFilename());
        }

        return $result;
    }
}
