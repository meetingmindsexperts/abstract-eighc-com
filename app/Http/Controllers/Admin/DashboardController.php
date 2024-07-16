<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessors\UserProfileAccessor;
use App\Models\Accessors\UsersAccessor;
use App\Models\Doc;
use App\Models\UserDocs;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $user;
    protected $userProfile;

    public function __construct()
    {
        $this->user = new UsersAccessor();

    }
    public function index(){
        $data = UserDocs::latest()->get();
        return view('admin.uploaded.view')->With('data',$data);
    }
    public function delete($id){
        UserDocs::where('id', $id)->update(['document' => '']);
        return back();
    }

}
