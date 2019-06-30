<?php
/**
 * VersionComponentsCollection
 *
 * Created at 2019-06-30 3:47 AM
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

use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Enuage\VersionUpdaterBundle\ValueObject\VersionComponent;

/**
 * Class VersionComponentsCollection
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionComponentsCollection extends ArrayCollection
{
    const DISABLE_ALL = false;
    const ENABLE_ALL = true;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $types = [], bool $enableAll = self::ENABLE_ALL)
    {
        $elements = [];
        foreach ($types as $type) {
            $elements[$type] = new VersionComponent(true === $enableAll);
        }

        parent::__construct($elements);
    }

    /**
     * @param $type
     * @param $value
     */
    public function set($type, $value)
    {
        if (Version::MAJOR === $type && $value > 0) {
            $this->get(Version::MINOR)->setValue(0);
            $this->get(Version::PATCH)->setValue(0);
        }

        if (Version::MINOR === $type) {
            if (0 === $value && 0 === $this->get(Version::MAJOR)->getValue()) {
                $value = 1;
            }

            if ($this->get(Version::MINOR)->getValue() !== $value) {
                $this->get(Version::PATCH)->setValue(0);
            }
        }

        $this->get($type)->setValue($value);
    }

    /**
     * @param $type
     *
     * @return VersionComponent
     */
    public function get($type): VersionComponent
    {
        return parent::get($type);
    }
}
