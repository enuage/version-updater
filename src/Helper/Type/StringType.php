<?php

namespace Enuage\VersionUpdaterBundle\Helper\Type;

use Enuage\VersionUpdaterBundle\Collection\ArrayCollection;

/**
 * Class StringType
 *
 * @author Serghei Niculaev <s.niculaev@dynatech.lv>
 */
class StringType
{
    public const REMOVE_EMPTY_ELEMENTS = 1;
    private const EMPTY_VALUE = '';

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $initialValue;

    /**
     * StringType constructor.
     *
     * @param string $value
     */
    public function __construct(string $value = self::EMPTY_VALUE)
    {
        $this->value = $value;
        $this->initialValue = $value;
    }

    /**
     * @return null|string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $delimiter
     * @param int $mode
     *
     * @return ArrayCollection
     *
     * @see  explode()
     * @link https://php.net/manual/en/function.explode.php
     */
    public function explode(string $delimiter, int $mode = 0): ArrayCollection
    {
        $result = $this->value ? explode($delimiter, $this->value) : [];

        if (self::REMOVE_EMPTY_ELEMENTS === $mode) {
            foreach ($result as $key => $element) {
                if (empty($element)) {
                    unset($result[$key]);
                }
            }
        }

        return new ArrayCollection($result);
    }

    /**
     * @param $search
     * @param $replace
     *
     * @return mixed
     *
     * @see  str_replace()
     * @link https://php.net/manual/en/function.str-replace.php
     */
    public function replace($search, $replace)
    {
        return str_replace($search, $replace, $this->value);
    }

    /**
     * @param string $string
     *
     * @return StringType
     */
    public function append(string $string): StringType
    {
        $this->value .= $string;

        return $this;
    }

    /**
     * @return StringType
     */
    public function reset(): StringType
    {
        $this->value = $this->initialValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getInitialValue(): string
    {
        return $this->initialValue;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isEqualTo(string $value): bool
    {
        return $value === $this->value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function startsWith(string $value): bool
    {
        return 0 === substr_compare($this->value, $value, 0, strlen($value));
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function endsWith(string $value): bool
    {
        if ('' === $this->value) {
            return false;
        }

        return 0 === substr_compare($this->value, $value, -strlen($value));
    }

    /**
     * @param string $value
     *
     * @return StringType
     */
    public function prepend(string $value): StringType
    {
        $this->value = $value.$this->value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return self::EMPTY_VALUE === $this->value;
    }

    /**
     * @return StringType
     */
    public function regexPrepare(): StringType
    {
        $this->value = preg_quote($this->value, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
