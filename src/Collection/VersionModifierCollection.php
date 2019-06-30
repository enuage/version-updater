<?php
/**
 * VersionModifierCollection
 *
 * Created at 2019-06-29 11:57 PM
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

use Enuage\VersionUpdaterBundle\ValueObject\VersionModifier;

/**
 * Class VersionModifierCollection
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionModifierCollection extends ArrayCollection
{
    /**
     * @var array
     */
    private $types;

    /**
     * VersionModifierCollection constructor.
     *
     * @param array $types
     * @param bool $isEnabled
     */
    public function __construct(array $types = [], bool $isEnabled = false)
    {
        $this->types = $types;

        $elements = [];
        foreach ($types as $type) {
            $elements[$type] = new VersionModifier($isEnabled);
        }

        parent::__construct($elements);
    }

    /**
     * @param bool $downgrade
     *
     * @return VersionModifierCollection
     */
    public function setDowngrade(bool $downgrade): VersionModifierCollection
    {
        foreach ($this->types as $type) {
            $this->get($type)->setDowngrade($downgrade);
        }

        return $this;
    }

    /**
     * @param $key
     *
     * @return VersionModifier
     */
    public function get($key): VersionModifier
    {
        return parent::get($key);
    }

    public function updateAll()
    {
        foreach ($this->types as $type) {
            $this->get($type)->update();
        }
    }
}
