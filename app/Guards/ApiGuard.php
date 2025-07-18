<?php

namespace App\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class ApiGuard implements Guard
{
    use GuardHelpers;

    protected $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return null;
        }

        $user = $accessToken->tokenable;
        
        if ($user && true // $user->is_active DISABLED) {
            $this->user = $user;
            return $user;
        }

        return null;
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);
            return true;
        }

        return false;
    }

    protected function getTokenFromRequest()
    {
        $token = $this->request->bearerToken();
        
        if (!$token) {
            $token = $this->request->input('api_token');
        }

        return $token;
    }
}
