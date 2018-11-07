<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 06/11/2018
 * Time: 23:18
 */

namespace Z1lab\OpenID\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Address extends Resource
{
    private $apiNamespace = 'api/v1';

    private $parent = 'users';

    private $type = 'addresses';

    public function toArray($request)
    {
        return [
            'data'  => [
                'type'       => $this->type,
                'id'         => $this->resource['relationships']['address']['id'],
                'attributes' => $this->resource['relationships']['address'],
            ],
            'links' => [
                'self' => config('openid.server') . "/{$this->apiNamespace}/{$this->parent}/{$this->resource['id']}/{$this->type}/{$this->resource['relationships']['address']['id']}",
            ],
        ];
    }
}