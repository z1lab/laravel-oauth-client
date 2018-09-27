<?php

namespace OpenID\Client\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogoutController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::guard()->logout();
            Request::session()->invalidate();
        }

        return new JsonResponse(['success' => TRUE]);
    }
}