<?php
/**
 * FormatterInterface
 *
 * Created at 2019-06-23 1:39 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Formatter;

/**
 * Interface FormatterInterface
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
interface FormatterInterface
{
    /**
     * @param mixed $subject
     *
     * @return mixed
     */
    public function format($subject = null);
}
