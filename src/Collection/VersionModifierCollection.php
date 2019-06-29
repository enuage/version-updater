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

use Closure;
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
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;

        $elements = [];

        $this->iterate(
            static function ($type) use (&$elements) {
                $elements[$type] = new VersionModifier();
            }
        );

        parent::__construct($elements);
    }

    /**
     * @param Closure $closure
     */
    private function iterate(Closure $closure)
    {
        foreach ($this->types as $type) {
            $closure($type);
        }
    }

    /**
     * @param bool $downgrade
     *
     * @return void
     */
    public function setDowngrade(bool $downgrade = false)
    {
        $this->iterate(
            function ($type) use ($downgrade) {
                $versionModifier = $this->get($type);
                $versionModifier->setDowngrade($downgrade);
            }
        );
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
}
