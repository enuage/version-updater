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
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
final class YamlHandler extends StructureHandler
{
    /**
     * {@inheritDoc}
     */
    public static function getExtensions(): array
    {
        return [
            'yaml',
            'yml',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function handle(FormatterInterface $formatter): string
    {
        return Yaml::dump($this->updateProperty($formatter), 2, 2);
    }

    /**
     * {@inheritDoc}
     */
    public function decodeContent(string $content): array
    {
        return Yaml::parse($content);
    }
}
