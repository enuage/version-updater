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
    public function __construct(string $value = '')
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
     *
     * @return ArrayCollection
     *
     * @see  explode()
     * @link https://php.net/manual/en/function.explode.php
     */
    public function explode(string $delimiter): ArrayCollection
    {
        $result = $this->value ? explode($delimiter, $this->value) : [];

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
}
