<?php
namespace App\Extensions\OAuth;

use Illuminate\Support\Facades\Auth;
use App\Entities\User;

class PasswordGrantVerifier
{
    public function verify($username, $password)
    {
        if(strlen($username)<3 || strlen($password)<6)
        {
            return false;
        }

        if (Auth::attempt(['username' => $username, 'password' =>$password])    ||
            Auth::attempt(['code' => $username, 'password' =>$password])        ||
            Auth::attempt(['mobile' => $username, 'password' =>$password])      ||
            Auth::attempt(['email' => $username, 'password' =>$password])
        )
        {
            return Auth::id();
        }


        else {
            return false;
        }
    }
}