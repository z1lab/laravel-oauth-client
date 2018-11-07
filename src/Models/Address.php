<?php

namespace Z1lab\OpenID\Models;

use Z1lab\OpenID\Traits\MagicMethodsTrait;

class Address
{
    use MagicMethodsTrait;

    /**
     * @var array
     */
    protected $attributes;

    protected $fillable = [
        'id',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'postal_code',
        'formatted',
    ];

    /**
     * Address constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        foreach (array_only($attributes, $this->fillable) as $key => $value) {
            $this->attributes[$key] = $value;
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
