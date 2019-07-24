<?php
/**
 * MetaComponent
 *
 * Created at 2019-06-30 3:38 PM
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
 * Class MetaComponent
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class MetaComponent
{
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_STRING = 'string';

    /**
     * @var string
     */
    private $type = self::TYPE_STRING;

    /**
     * @var null|string
     */
    private $format;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return MetaComponent
     */
    public function setType(string $type): MetaComponent
    {
        $this->type = $type;

        if (self::TYPE_DATETIME === $type && null === $this->format) {
            $this->format = 'c';
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param null|string $format
     *
     * @return MetaComponent
     */
    public function setFormat(string $format): MetaComponent
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return MetaComponent
     */
    public function setValue($value): MetaComponent
    {
        $this->value = $value;

        return $this;
    }
}
