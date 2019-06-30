<?php
/**
 * FilesNormalizer
 *
 * Created at 2019-06-23 2:02 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Normalizer;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;

/**
 * Class FilesNormalizer
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class FilesArrayNormalizer
{
    /**
     * Regenerate files array to array<filePath, pattern>
     *
     * Input:
     * ```php
     * [
     *    0 => [
     *        ".env" => "/^(API_VERSION=)\V/m",
     *    ],
     *    1 => [
     *        "README.md" => "/^(Version:\s)\V/m",
     *    ],
     * ]
     * ```
     *
     * Output:
     * ```
     * [
     *     ".env" => "/^(API_VERSION=)\V/m",
     *     "README.md" => "/^(Version:\s)\V/m",
     * ]
     * ```
     *
     * @param $subject
     *
     * @return ArrayCollection
     */
    public static function normalize($subject): ArrayCollection
    {
        $files = new ArrayCollection();

        if (!empty($subject) && is_array($subject)) {
            array_walk_recursive($subject, static function ($value, $key) use (&$files) {
                if (!is_numeric($key)) {
                    $files->set($key, $value);
                }
            });
        }

        return $files;
    }
}
