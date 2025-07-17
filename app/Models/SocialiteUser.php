<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class SocialiteUser implements FilamentSocialiteUserContract
{
    public function getUser(): Authenticatable
    {
        //
        return auth()->user();
    }

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self
    {
        //

        return new static();
    }
    
    public static function createForProvider(
        string $provider,
        SocialiteUserContract $oauthUser,
        Authenticatable $user
    ): self {
        //

        return new static();
    }
}