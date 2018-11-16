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
     * @var string
     */
    private $apiNamespace = 'api/v1';
    /**
     * @var string
     */
    private $type = 'users';

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'type'       => $this->type,
            'id'         => $this->resource['id'],
            'attributes' => [
                'id'             => $this->resource['id'],
                'name'           => $this->resource['name'],
                'document'       => $this->resource['document'],
                'social_name'    => $this->resource['social_name'],
                'nickname'       => $this->resource['nickname'],
                'username'       => $this->resource['username'],
                'email'          => $this->resource['email'],
                'avatar'         => $this->resource['avatar'],
                'phone'          => $this->resource['phone'],
                'phone_verified' => $this->resource['phone_verified'],
                'email_verified' => $this->resource['email_verified'],
                'roles'          => $this->resource['roles'],
                'birthdate'      => $this->resource['birthdate'],
                'auth_time'      => $this->resource['auth_time'],
                'updated_at'     => $this->resource['updated_at'],
                'expires_at'     => $this->resource['expires_at'],
            ],
        ];

        $data['relationships']['address'] = $this->resource['relationships']['address'] !== NULL
            ? new Address($this->resource)
            : NULL;

        return $data;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'links' => [
                'self' => config('openid.server') . "/{$this->apiNamespace}/{$this->type}/{$this->resource['id']}",
            ],
        ];
    }
}
