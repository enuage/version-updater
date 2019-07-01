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
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class YamlHandler extends FormatHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(FileParser $parser, FormatterInterface $formatter): string
    {
        $this->setParser($parser);

        $content = $this->decodeContent();
        $this->updateProperty($content, $formatter);

        return Yaml::dump($content, 2, 2);
    }

    /**
     * @return array
     */
    private function decodeContent(): array
    {
        return Yaml::parse($this->getParser()->getFile()->getContents());
    }

    /**
     * {@inheritDoc}
     */
    public function getFileContent(FileParser $parser): string
    {
        $this->setParser($parser);

        $content = $this->decodeContent();

        return $this->getValue($content);
    }
}
