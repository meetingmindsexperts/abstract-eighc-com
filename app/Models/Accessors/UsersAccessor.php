<?php


namespace App\Models\Accessors;

use App\Mail\DynamicMailer;
use App\Models\PasswordResetsModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Mail;
class UsersAccessor
{

    public function __construct()
    {

    }


        public function login($data, $role = '')
    {
        $rememberMe = false;
        if(isset($data['remember_me'])){
            $rememberMe = true;
        }

        $auth = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $rememberMe);
        if ($auth) {
            return true;
        } else {
            return false;
        }
    }

}

