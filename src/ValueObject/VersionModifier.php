<?php
/**
 * MainVersion
 *
 * Created at 2019-06-29 11:48 PM
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
 * Class VersionModifier
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionModifier
{
    private const MAXIMAL_MODIFIER_VALUE = 1;
    private const IGNORED_MODIFIER_VALUE = 0;
    private const MINIMAL_MODIFIER_VALUE = -1;

    /**
     * @var int
     */
    private $modifier = self::IGNORED_MODIFIER_VALUE;

    /**
     * @var bool
     */
    private $downgrade = false;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * VersionModifier constructor.
     *
     * @param bool $isEnabled
     */
    public function __construct(bool $isEnabled = false)
    {
        $this->enabled = $isEnabled;
    }

    /**
     * @return VersionModifier
     */
    public function update(): VersionModifier
    {
        if ($this->isEnabled()) {
            if ($this->isDowngrade()) {
                if (self::MINIMAL_MODIFIER_VALUE < $this->modifier) {
                    $this->modifier--;
                }
            } elseif (self::MAXIMAL_MODIFIER_VALUE > $this->modifier) {
                $this->modifier++;
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isDowngrade(): bool
    {
        return $this->downgrade;
    }

    /**
     * @param bool $downgrade
     *
     * @return VersionModifier
     */
    public function setDowngrade(bool $downgrade): VersionModifier
    {
        $this->downgrade = $downgrade;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUpdated(): bool
    {
        return self::IGNORED_MODIFIER_VALUE !== $this->modifier;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->modifier;
    }

    /**
     * @return void
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * @return void
     */
    public function disable(): void
    {
        $this->enabled = false;
    }
}
