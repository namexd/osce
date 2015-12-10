<?php
namespace App\Extensions\OAuth;

use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($username, $password)
    {
        $persons=Student::where('code','=',$username)->get();
        $person=$persons->first();
        if(empty($person))
        {
            $persons=Teacher::where('code','=',$username);
            $person=$persons->first();
        }
        if(is_null($person))
        {
            $id='';
            return false;
        }
        else
        {
            $id=$person->id;
        }
        if (!is_null($id)&&Auth::attempt(['id' => $id, 'password' =>$password]))
        {
            return Auth::id();
        }
//        if (Auth::attempt(['username' => $username, 'password' =>$password]))
//        {
//            return Auth::id();
//        } else {
//            return false;
//        }
    }
}