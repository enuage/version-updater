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
 *
 * @method VersionModifier get($key, $default = null)
 */
class VersionModifierCollection extends ArrayCollection
{
    public const DISABLE_ALL = false;
    public const ENABLE_ALL = true;

    /**
     * VersionModifierCollection constructor.
     *
     * @param array $types
     * @param bool $enableAll
     */
    public function __construct(array $types = [], bool $enableAll = self::DISABLE_ALL)
    {
        parent::__construct();

        foreach ($types as $type) {
            $this->set($type, new VersionModifier(true === $enableAll));
        }
    }

    /**
     * @param string $type
     */
    public function enable(string $type): void
    {
        $this->get($type)->enable();
    }

    /**
     * @param string $type
     */
    public function decrease(string $type): void
    {
        $this->get($type)->setDowngrade(true)->update();
    }

    /**
     * @param string $type
     */
    public function increase(string $type): void
    {
        $this->get($type)->setDowngrade(false)->update();
    }

    /**
     * @param bool $downgrade
     *
     * @return VersionModifierCollection
     */
    public function downgradeAll(bool $downgrade): VersionModifierCollection
    {
        /** @var VersionModifier $type */
        foreach ($this->getIterator() as $type) {
            $type->setDowngrade($downgrade);
        }

        return $this;
    }
}
