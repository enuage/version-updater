<?php
/**
 * ArrayCollection
 *
 * Created at 2019-06-23 3:47 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Collection;

use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;

/**
 * Class ArrayCollection
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class ArrayCollection extends DoctrineArrayCollection
{
    /**
     * @param string $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getValue(string $key, $default = null)
    {
        $value = $this->get($key);

        if (!$value) {
            $value = $default;
        }

        return $value;
    }
}
