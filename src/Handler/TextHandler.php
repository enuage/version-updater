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
final class TextHandler extends AbstractHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(FormatterInterface $formatter): string
    {
        $matches = $this->getParser()->getMatches();
        $lastMatch = $matches->last();
        $lastMatchValue = !is_numeric($lastMatch) && $matches->count() > 12 ? $lastMatch : '';

        $content = preg_replace(
            $this->getPattern(),
            sprintf('${1}%s%s', $formatter->format(), $lastMatchValue),
            parent::getFileContent()
        );

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        return $this->pattern->replace(FileParser::FILE_VERSION_PATTERN, FileParser::VERSION_PATTERN);
    }
}
