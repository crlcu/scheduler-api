<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()
                    ->json(['error' => 'invalid_credentials'], 401)
                    ->withCallback($request->input('callback'));
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()
                ->json(['error' => 'could_not_create_token'], 500)
                ->withCallback($request->input('callback'));
        }

        return response()
            ->json([
                'success'   => true,
                'token'     => $token,
                'user'      => Auth::user(),
            ])
            ->withCallback($request->input('callback'));
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()
            ->json(['success' => true])
            ->withCallback($request->input('callback'));
    }
}
