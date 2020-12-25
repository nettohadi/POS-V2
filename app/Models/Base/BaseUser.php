<?php

namespace App\Models\Base;

use App\Exceptions\ApiAuthenticationException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Hash;

class BaseUser extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    public static function findByEmailOrThrow($email){
        $message = __('auth.email');
        return self::where('email',$email)->firstOrThrow($message);
    }

    public function checkPassword($password){
        if (! Hash::check($password, $this->attributes['password'])) {
            $message = __('auth.password');
            throw new ApiAuthenticationException($message);
        }else{
            return true;
        }
    }
}
