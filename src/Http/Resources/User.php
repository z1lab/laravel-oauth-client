<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 06/11/2018
 * Time: 22:29
 */

namespace Z1lab\OpenID\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'users',
            'id' => $this->resource['id'],
            'attributes' => parent::toArray($request)
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'links' => [
                'self' => route('openid.user')
            ]
        ];
    }
}
