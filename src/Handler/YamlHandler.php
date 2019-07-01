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
class YamlHandler extends AbstractHandler
{
    /**
     * @var FileParser
     */
    private $parser;

    /**
     * {@inheritDoc}
     */
    public function handle(FileParser $parser, FormatterInterface $formatter): string
    {
        $this->parser = $parser;

        $content = $this->decodeContent();

        $this->accessProperty(
            $content,
            $this->getProperties(),
            static function (&$property) use ($formatter) {
                $property = $formatter->format();
            }
        );

        return Yaml::dump($content, 2, 2);
    }

    /**
     * @return array
     */
    private function decodeContent(): array
    {
        return Yaml::parse($this->parser->getFile()->getContents());
    }

    /**
     * @return array
     */
    private function getProperties(): array
    {
        return explode('/', $this->pattern);
    }

    /**
     * {@inheritDoc}
     */
    public function getFileContent(FileParser $parser): string
    {
        $this->parser = $parser;
        $content = $this->decodeContent();

        $this->accessProperty(
            $content,
            $this->getProperties(),
            static function ($property) use (&$value) {
                $value = $property;
            }
        );

        return $value;
    }
}
