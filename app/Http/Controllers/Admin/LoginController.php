<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Accessors\UsersAccessor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private $user;
    public function __construct()
    {
        $this->user = new UsersAccessor();
    }
    public function login(){
        return view('admin.login');
    }

    public function submit(Request $request){
        // echo Hash::make($request->password);
        // die();
        $res = $this->user->login($request->all(), User::$ADMIN);
        if($res){

            \Session::flash('success', 'You have Logged In Successfully');
            return redirect()->route('admin.dashboard');

        } else {
            \Session::flash('error', 'Wrong Email or Password');
        }
        return redirect(route('admin.admin-login'));
    }

    public function logout(){
        $this->user->logout();
        \Session::flash('success', 'You have Logged out Successfully');
        return redirect()->route('admin.admin-login');
    }

}
