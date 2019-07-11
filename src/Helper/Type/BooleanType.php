<?php

namespace Enuage\VersionUpdaterBundle\Helper\Type;

/**
 * Class BooleanType
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class BooleanType
{
    /**
     * @param bool $value
     *
     * @return string
     */
    public static function toShortStatement(bool $value): string
    {
        return $value ? 'Yes' : 'No';
    }
}
