<?php
/**
 * FileNotFoundException
 *
 * Created at 2019-07-05 11:37 PM
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

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;
use Exception;

/**
 * Class FileNotFoundException
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FileNotFoundException extends Exception implements EnuageExceptionInterface
{
    /**
     * FileNotFoundException constructor.
     *
     * @param string $directory
     * @param string $fileName
     * @param ArrayCollection $extensions
     */
    public function __construct(string $directory, string $fileName, ArrayCollection $extensions)
    {
        parent::__construct(
            sprintf(
                'No file found with name "%s" in the directory "%s". Expected extensions: "%s".',
                $fileName,
                $directory,
                $extensions->implode(',')
            ),
            404
        );
    }
}
