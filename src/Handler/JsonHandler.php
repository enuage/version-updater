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

use Enuage\VersionUpdaterBundle\Formatter\FormatterInterface;
use Enuage\VersionUpdaterBundle\Parser\FileParser;

/**
 * Class JsonHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
final class JsonHandler extends StructureHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(FormatterInterface $formatter): string
    {
        $content = $this->decodeContent();
        $this->updateProperty($content, $formatter);

        return json_encode($content, JSON_PRETTY_PRINT).PHP_EOL;
    }

    /**
     * {@inheritDoc}
     */
    protected function decodeContent(): array
    {
        return json_decode($this->getParser()->getFile()->getContents(), true);
    }
}
