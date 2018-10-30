<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 30/10/2018
 * Time: 18:13
 */

namespace OpenID\Client\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp\Client;
use Illuminate\View\View;
use OlimarFerraz\LaravelMeta\Facade as Meta;

class EmailConfirmationController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function index(Request $request)
    {
        Meta::set('title', __('Email confirmation'));


        $client = new Client(['base_uri' => config('openid.server')]);
        $status = FALSE;
        
        try {
            $client->get(base64_decode($request->get('token')));
            $status = TRUE;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return View('auth.email-confirm')->with('confirmed', $status);
    }
}
