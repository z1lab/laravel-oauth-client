<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 05/11/2018
 * Time: 11:23
 */

namespace Z1lab\OpenID\Models;

use Z1lab\OpenID\Traits\MagicMethodsTrait;

class Phone
{
    use MagicMethodsTrait;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Phone constructor.
     *
     * @param string|NULL $phone
     */
    public function __construct(string $phone = NULL)
    {
        if ($phone !== NULL) {
            $this->attributes['formatted'] = $phone;

            $phone = preg_replace('/[^0-9]/', '', $phone);

            $this->attributes['area_code'] = substr($phone, 0, 2);
            $this->attributes['number'] = substr($phone, 2);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
