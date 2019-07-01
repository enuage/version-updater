<?php
/**
 * YamlHandler
 *
 * Created at 2019-07-01 7:12 AM
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
 * Class YamlHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class YamlHandler extends AbstractHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(FileParser $parser, FormatterInterface $formatter): string
    {
        // TODO: Implement handle() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getFileContent(FileParser $parser): string
    {
        // TODO: Implement getFileContent() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        // TODO: Implement getPattern() method.
    }
}
