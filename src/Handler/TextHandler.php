<?php
/**
 * TextHandler
 *
 * Created at 2019-06-30 11:39 PM
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
 * Class TextHandler
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class TextHandler extends AbstractHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(FileParser $parser, FormatterInterface $formatter): string
    {
        $matches = $parser->getMatches();
        $lastMatch = $matches->last();
        $lastMatchValue = !is_numeric($lastMatch) && $matches->count() > 12 ? $lastMatch : '';

        $file = $parser->getFile();

        $content = preg_replace(
            $this->getPattern(),
            sprintf('${1}%s%s', $formatter->format(), $lastMatchValue),
            $file->getContents()
        );

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        return str_replace(FileParser::FILE_VERSION_PATTERN, FileParser::VERSION_PATTERN, $this->pattern);
    }

    /**
     * {@inheritDoc}
     */
    public function getFileContent(FileParser $parser): string
    {
        return $parser->getFile()->getContents();
    }
}
