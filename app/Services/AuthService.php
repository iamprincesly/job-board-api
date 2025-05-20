<?php

namespace App\Services;

use App\Enums\AuthScope;
use Illuminate\Foundation\Auth\User;

class AuthService
{
    /**
     * This creates and return the token string
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \App\Enums\AuthScope $scope
     *
     * @return array
     */
    public function createToken(User $user, AuthScope $scope): array
    {
        $token = $user->createToken(config('app.name') . '-Auth-Grant-Client', [$scope->value]);

        $data = [
            'token' => $token->accessToken,
            'type' => 'Bearer',
            'scope' => $scope,
            'expires_at' => $token->token->expires_at->getTimestamp(),
        ];

        $user->forceFill(['last_login_at' => now()])->save();

        return $data;
    }
}
