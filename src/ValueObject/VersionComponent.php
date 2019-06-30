<?php
/**
 * VersionComponent
 *
 * Created at 2019-06-30 3:45 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\ValueObject;

/**
 * Class VersionComponent
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionComponent
{
    /**
     * @var int
     */
    private $value = 0;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * VersionComponent constructor.
     *
     * @param bool $enabled
     */
    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }
}
