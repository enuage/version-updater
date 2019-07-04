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
     * @var string|null
     */
    private $value;

    /**
     * StringType constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
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
}
