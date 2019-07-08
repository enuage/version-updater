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
use Doctrine\Common\Collections\Collection;
use Enuage\VersionUpdaterBundle\Helper\Type\StringType;
use Iterator;

/**
 * Class ArrayCollection
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class ArrayCollection extends DoctrineArrayCollection
{
    /**
     * @param Iterator $iterator
     *
     * @return ArrayCollection
     */
    public static function fromIterator(Iterator $iterator): ArrayCollection
    {
        return new self(iterator_to_array($iterator));
    }

    /**
     * @param string $key
     * @param null|mixed $default
     *
     * @return mixed|null
     */
    public function getValue(string $key, $default = null)
    {
        return $this->containsKey($key) ? $this->get($key) : $default;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return int
     */
    public function getIntValue(string $key, $default = null): int
    {
        return (int) $this->getValue($key, $default);
    }

    /**
     * @param string $glue
     *
     * @return StringType
     */
    public function implode(string $glue): StringType
    {
        return new StringType(implode($glue, $this->toArray()));
    }

    /**
     * @param $elements
     *
     * @return ArrayCollection
     */
    public function append($elements): ArrayCollection
    {
        if (is_array($elements)) {
            array_map(
                function ($element) {
                    $this->add($element);
                },
                $elements
            );
        }

        if ($elements instanceof Collection) {
            $this->append($elements->toArray());
        }

        return $this;
    }

    /**
     * @param $current
     *
     * @return null|mixed
     */
    public function getNext($current)
    {
        $result = null;
        $iterator = $this->getIterator();

        while ($iterator->valid()) {
            if ($current === $iterator->current()) {
                $iterator->next();

                $result = $iterator->current();

                break;
            }

            $iterator->next();
        }

        return $result;
    }
}
