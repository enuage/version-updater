<?php
/**
 * InvalidFileException
 *
 * Created at 2019-07-11 12:38 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Exception;

use Exception;

/**
 * Class InvalidFileException
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class InvalidFileException extends Exception implements EnuageExceptionInterface
{
    /**
     * InvalidFileException constructor.
     *
     * @param string $directory
     * @param string $fileName
     */
    public function __construct(string $directory, string $fileName)
    {
        parent::__construct(
            sprintf(
                'You have provided invalid file for parsing. Please check again file "%s%s".',
                $directory,
                $fileName
            ),
            404
        );
    }
}
