<?php
namespace App\Extensions\OAuth;

use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($username, $password)
    {
        if (Auth::attempt(['username' => $username, 'password' =>$password]))
        {
            return Auth::id();
        } else {
            return false;
        }
    }
}