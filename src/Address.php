<?php

namespace OpenID\Client;

use OpenID\Client\Traits\MagicMethodsTrait;

class Address
{
    use MagicMethodsTrait;

    /**
     * @var array
     */
    protected $attributes;

    protected $fillable = ['street', 'number', 'complement', 'district', 'city', 'state', 'postal_code'];

    /**
     * Address constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        foreach (array_only($attributes, $this->fillable) as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }
}