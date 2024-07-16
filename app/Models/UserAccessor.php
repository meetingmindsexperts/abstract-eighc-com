<?php

namespace App\Models\Accessors;

use App\Models\User;
use App\Models\UserAdmin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

use App\Models\EnrolledWebinars;
use App\Models\EnrolledClasses;
use App\Mail\EmailVerification;
use App\Mail\InviteToCourse;
use App\Mail\InviteVerification;
use App\Mail\ForgetPassword;
use App\Models\EnrolledStudents;
use App\Models\Roles;
use Carbon\Carbon;
use Mail;

class UserAccessor
{

    public function __construct()
    {

    }
    /**
     *
     * Verify user Details
     *
     * @param $data
     * @return bool
     *
     */
    // public function login($data, $role)
    // {
    //     $rememberMe = false;
    //     if(isset($data['remember_me'])){
    //         $rememberMe = true;
    //     }

    //     $auth = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $rememberMe);
    //     if ($auth) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }


    /**
     *
     * User Logout function
     *
     * @return bool
     */
    public function userLogout(){
        Auth::logout();
        return true;
    }
    /**
     * Save user information
     *
     * @param $data
     * @return Users
     */
    public function saveUsers($data)
    {
        $users = new User();

        $users->name = $data['name'];
        $users->email = $data['email'];
        $users->role_id = $data['role_id'];
        $users->status  = $data['status'];
        $users->password = Hash::make($data['password']);
        $users->save();
        return $users;
    }

    /**
     *
     * Get all users with roles
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
      $users =  User::where('role_id', '!=', User::$ADMIN)->with('role')->get();
      return $users;
    }

    public function getAllInstructors()
    {

        $users =  User::where('role_id', User::$INSTRUCTOR)->get();
       return $users;
    }


    public function getEmailsOfStudents()
    {

        $users =  User::where('role_id', User::$STUDENT)->get();
        return $users;

    }

    /**
     *
     * Get user information by ID
     *
     * @param $id
     * @return mixed
     */

    public function getUsersById($id)
    {
        $id = Crypt::decrypt($id);
        return User::find($id);
    }

    public function deleteUsers($id)
    {
        $id = Crypt::decrypt($id);
        return User::find($id)->delete();
        return true;
    }

    public function getUserByPlainId($id){
        return User::find($id);
    }

    public function updateUsers($data)
    {

        $id = Crypt::decrypt($data['id']);

        $arr = ['name' => $data['name'],
                 'email' => $data['email'],
                 'role_id' => $data['role_id'],
                 'status'   => $data['status'],
                  'password' => $data['password']
            ];
        User::where('id', $id)->update($arr);
        return true;

    }

    public function getUserByEmail($email){
        return User::where('email',$email)->first();
    }

    public function updatePassword($data){
        User::where('id', Auth::user()->id)->update(['password' => Hash::make($data['password'])]);
        return true;
    }
    public function updateProfile($data){

        $update = ['name' => $data['name'], 'phone' => $data['phone']];
        if($data['image'] != null){
            $uploadedImageName = $data['image']->store('profile');
            $update = $update + ['image' => $uploadedImageName];
            Auth::user()->image = $uploadedImageName;
        }

        User::where('id', Auth::user()->id)->update($update);
        return true;
    }

    public function getUserWithVerificationToken($id){
        return User::with('verify')->where('id', $id)->get();
    }

    public function getRegisteredUsers(){
        return User::where('role_id', User::$STUDENT)->get();
    }
    public function getUserByParentId($id){
        return User::where('parent_id', $id)->get();
    }
    public function updateFromUserToMultiUser(){

        User::where('id', Auth::user()->id)->update(['role_id' => User::$GROUP_USER]);
        Auth::user()->role_id = User::$GROUP_USER;
        return true;
    }
    public function updateZohoCustomerId($zohoId){
        User::where('id', Auth::user()->id)->update(['zoho_customer_id' => $zohoId]);
        Auth::user()->zoho_customer_id = $zohoId;
        return true;
    }

    public function getRegisteredUsersForInstructor()
    {
       // return User::where('role_id', User::$STUDENT)->get();

       // return EnrolledWebinars::with('user', 'webinar')->where('user.role_id', User::$STUDENT)->get();

          $enroll_webinars = EnrolledWebinars::query()
          ->select('enrolled_webinars.*')
          ->join('users', 'users.id', '=', 'enrolled_webinars.user_id')
          ->join('webinar', 'webinar.id', '=', 'enrolled_webinars.webinar_id')
          ->where('users.role_id',  User::$STUDENT)
          ->where('webinar.zoom_type', 1)
          ->Where('webinar.instructor_id',  Auth::user()->id)->latest()
          ->get();

          return $enroll_webinars;


    }

    public function getRegisteredUsersForInstructorCourses()
    {

        $enroll_class = EnrolledClasses::query()
        ->select('enrolled_classes.*')
        ->join('users', 'users.id', '=', 'enrolled_classes.user_id')
        ->join('courses', 'courses.id', '=', 'enrolled_classes.class_id')
        ->where('users.role_id',  User::$STUDENT)
        ->Where('courses.instructor_id',  Auth::user()->id)->latest()
        ->get();

        return $enroll_class;

    }

    public function getEnrolledClassroomForInstructor()
    {

        $enroll_onlineclass = EnrolledWebinars::query()
        ->select('enrolled_webinars.*')
        ->join('users', 'users.id', '=', 'enrolled_webinars.user_id')
        ->join('webinar', 'webinar.id', '=', 'enrolled_webinars.webinar_id')
        ->where('users.role_id',  User::$STUDENT)
        ->where('webinar.zoom_type', 2)
        ->Where('webinar.instructor_id',  Auth::user()->id)->latest()
        ->get();

        return $enroll_onlineclass;
    }

    public function saveUser($data)
    {
        $Users = new User();

        $Users->name = $data['name'];
        $Users->email = $data['email'];
        $Users->password = Hash::make($data['password']);
        $Users->role_id = $data['role_id'];
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        $Users->save();
        return true;
    }

    public function saveUserFromWebsite($data)
    {
        $Users = new User();
        $Users->first_name = $data['first_name'];
        $Users->last_name = $data['last_name'];
        $Users->name = $data['first_name'] .' '.$data['last_name'];
        $Users->email = $data['email'];
        //$Users->username = $data['username'];
        $Users->phone = $data['phone'];
        $Users->company = $data['company'];
        $Users->country = $data['country'];
        $Users->city = $data['city'];
        $Users->state = $data['state'];
        $Users->occupation = $data['occupation'];
        $Users->password = Hash::make($data['password']);
        $Users->role_id = $data['type'];
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        $Users->save();
        $this->verifyUser->save($Users->id);
        $this->userInterests->save($data, $Users->id);
        Mail::to($data['email'])->send(new EmailVerification($Users->id));
        return true;
    }

    // public function getAllUsers()
    // {
    //     return User::with('role')->where('role_id', '!=', User::$STUDENT)->where('role_id', '!=', User::$ADMIN)->get();
    // }

     public function getUsers()
    {
        return User::all();

    }

    public function getUserById($id)
    {
        $id = Crypt::decrypt($id);

        return User::where('id', $id)->firstorfail();

    }

    public function getEmails($usersIDs)
    {


        return User::with('role')->whereIn('id', [$usersIDs])->get();

    }

    public function updateUser($data)
    {
        $arr = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'remember_token' => substr(md5(uniqid(rand(), true)), 0, 10),
        );
        User::where('id', $data['id'])->update($arr);
        return true;

    }

    public function deleteUser($id)
    {
        $id = Crypt::decrypt($id);

        User::where('id', $id)->delete();

        return true;
    }

    public function getRoles()
    {
        return Roles::where('id', '!=', 1)->get();
    }

    public function checkEmail($email)
    {
        $count = User::where('email', $email)->count();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkUserName($username)
    {
        $count = User::where('username', $username)->count();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function verifyEmailAddress($token)
    {
        $verifyToken = $this->verifyUser->getUserWtihToken($token);
        if (empty($verifyToken)) {
            return false;
        }
        $user = User::find($verifyToken->user_id);
        if (empty($user->email_verified_at)) {
            $array = ['email_verified_at' => Carbon::now()];
            $this->verifyUser->deleteTokenById($verifyToken->id);
            User::with('id', $verifyToken)->update($array);
        }

        $arr = array(
            'status' => 1
        );
        User::where('id', $verifyToken->user_id)->update($arr);
        return true;
    }

    public function login($data)
    {
        $rememberMe = false;
        if (isset($data['remember_me'])) {
            $rememberMe = true;
        }
        $auth = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $rememberMe);
        if ($auth) {
            return true;
        } else {
            return false;
        }
    }

    public function forgetPassword($email)
    {
        $user = User::where('email', $email);
        if ($user->count() == 1) {
            $token = $this->passwordReset->save($email);
            Mail::to($email)->send(new ForgetPassword($user->first(), $token));
            return true;
        } else {
            return false;
        }
    }

    public function verifyForgetPasswordToken($token)
    {
        $verifyToken = $this->passwordReset->getUserByToken($token);
        if (empty($verifyToken)) {
            return false;
        }
        return $token;
    }

    public function setNewPassword($data)
    {
        $email = $this->passwordReset->deleteToken($data['token']);
        if (empty($email->email)) {
            return false;
        }
        $u = User::where('email', $email->email)->update(['password' => Hash::make($data['password'])]);
        if ($u) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUserOnDashboard($data)
    {
        $arr = array(
            'name' => $data['name'],
            'phone' => $data['phone'],
        );
        if(isset($data['profile_image'])){
            $image = $data['profile_image']->store('Profile');
            $arr = $arr + ['profile_image' => $image];
            Auth::user()->profile_image = $image;
        }

        User::where('id', $data['id'])->update($arr);
        return true;

    }

    public function checkEmailInvition($email)
    {
        $email = User::where('email', $email)->get();
        $data = "";
        if (count($email) > 0) {
            $data = $email;
            return $data;
        } else {

            return $data;
        }
    }

    public function saveInvitionStudent($data)
    {
        $password = substr(uniqid(rand(), true), 0, 10);
        $id = Crypt::decrypt($data['user_id']);
        $course_id = Crypt::decrypt($data['course_id']);
        $lpo_request_id = Crypt::decrypt($data['request_id']);
        $course_name=$data['course_name'];
        $Users = new User();
        $Users->name = $data['name'];
        $Users->email = $data['email'];
        $Users->parent_id = $id;
        $Users->password = Hash::make($password);
        $Users->role_id = 3;
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        if ($Users->save()) {
            $EnrolledStudents = new EnrolledStudents();
            $EnrolledStudents->course_id = $course_id;
            $EnrolledStudents->user_id = $Users->id;
            $EnrolledStudents->lpo_request_id = $lpo_request_id;
            $EnrolledStudents->expiring_at = Carbon::now();
            $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
            if ($EnrolledStudents->save()) {
                $this->verifyUser->save($Users->id);
                Mail::to($data['email'])->send(new InviteVerification($Users->id,$password,$course_name));
                return true;
            } else {
                return false;

            }
        }
    }
    public function saveInvitionenrolledStudents($data,$user_id,$username){
        $course_id = Crypt::decrypt($data['course_id']);
        $lpo_request_id = Crypt::decrypt($data['request_id']);
        $course_name=$data['course_name'];
        $EnrolledStudents = new EnrolledStudents();
        $EnrolledStudents->course_id = $course_id;
        $EnrolledStudents->user_id = $user_id;
        $EnrolledStudents->lpo_request_id = $lpo_request_id;
        $EnrolledStudents->expiring_at = Carbon::now();
        $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
        if ($EnrolledStudents->save()) {
            Mail::to($data['email'])->send(new InviteToCourse($username,$course_name));
            return true;
        } else {
            return false;

        }


    }
    public function saveInvitionStudentByImport($lpo_request_id,$course_id,$course_name,$email,$name)
    {
        $password = substr(uniqid(rand(), true), 0, 10);
        $lpo_request_id = Crypt::decrypt($lpo_request_id);
        $Users = new User();
        $Users->name = $name??"trainee";
        $Users->email = $email;
        $Users->parent_id = Auth::user()->id;
        $Users->password = Hash::make($password);
        $Users->role_id = 3;
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        if ($Users->save()) {
            $EnrolledStudents = new EnrolledStudents();
            $EnrolledStudents->course_id = $course_id;
            $EnrolledStudents->user_id = $Users->id;
            $EnrolledStudents->lpo_request_id = $lpo_request_id;
            $EnrolledStudents->expiring_at = Carbon::now();
            $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
            if ($EnrolledStudents->save()) {
                $this->verifyUser->save($Users->id);
                Mail::to($email)->send(new InviteVerification($Users->id,$password,$course_name));
                return true;
            } else {
                return false;

            }
        }
    }
    public function saveInvitionenrolledStudentsByImport($lpo_request_id,$course_id,$course_name,$user_id,$username,$email){
        $lpo_request_id = Crypt::decrypt($lpo_request_id);
        $EnrolledStudents = new EnrolledStudents();
        $EnrolledStudents->course_id = $course_id;
        $EnrolledStudents->user_id = $user_id;
        $EnrolledStudents->lpo_request_id = $lpo_request_id;
        $EnrolledStudents->expiring_at = Carbon::now();
        $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
        if ($EnrolledStudents->save()) {
            Mail::to($email)->send(new InviteToCourse($username,$course_name));
            return true;
        } else {
            return false;

        }

    }
    public function checkEnrollesInvition($request_id,$user_id)
    {
        $request_id = Crypt::decrypt($request_id);
        $enrolled = EnrolledStudents::where('user_id', $user_id)->where('lpo_request_id', $request_id)->get();
        $data = 0;
        if (count($enrolled) > 0) {
            $data = 1;
            return $data;
        } else {

            return $data;
        }
    }

    public function inActiveUser($id){
        $id = Crypt::decrypt($id);
        return User::where('id', $id)->update(['status' => 0]);
    }

    public function ActiveUser($id){
        $id = Crypt::decrypt($id);
        return User::where('id', $id)->update(['status' => 1]);
    }

    public function getCountryCandites()
    {
         return User::groupBy('country')->get();

         //$tasks->groupBy('category_id')->map->count();

        /* $user_info = DB::table('usermetas')
                 ->select('browser', DB::raw('count(*) as total'))
                 ->groupBy('browser')
                 ->get();*/

    }


    /*public function saveUser($data)
    {
        $Users = new User();

        $Users->name = $data['name'];
        $Users->email = $data['email'];
        $Users->password = Hash::make($data['password']);
        $Users->role_id = $data['role_id'];
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        $Users->save();
        return true;
    }

    public function saveUserFromWebsite($data)
    {
        $Users = new User();
        $Users->first_name = $data['first_name'];
        $Users->last_name = $data['last_name'];
        $Users->name = $data['first_name'] .' '.$data['last_name'];
        $Users->email = $data['email'];
        //$Users->username = $data['username'];
        $Users->phone = $data['phone'];
        $Users->company = $data['company'];
        $Users->country = $data['country'];
        $Users->city = $data['city'];
        $Users->occupation = $data['occupation'];
        $Users->password = Hash::make($data['password']);
        $Users->role_id = $data['type'];
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        $Users->save();
        $this->verifyUser->save($Users->id);
        $this->userInterests->save($data, $Users->id);
        Mail::to($data['email'])->send(new EmailVerification($Users->id));
        return true;
    }

    public function getAllUsers()
    {
        return User::with('role')->where('role_id', '!=', User::$STUDENT)->where('role_id', '!=', User::$ADMIN)->get();
    }

     public function getUsers()
    {
        return User::all();

    }

    public function getUserById($id)
    {
        $id = Crypt::decrypt($id);

        return User::where('id', $id)->firstorfail();

    }

    public function getEmails($usersIDs)
    {


        return User::with('role')->whereIn('id', [$usersIDs])->get();

    }

    public function updateUser($data)
    {
        $arr = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'remember_token' => substr(md5(uniqid(rand(), true)), 0, 10),
        );
        User::where('id', $data['id'])->update($arr);
        return true;

    }

    public function deleteUser($id)
    {
        $id = Crypt::decrypt($id);

        User::where('id', $id)->delete();

        return true;
    }

    public function getRoles()
    {
        return Roles::all();
    }

    public function checkEmail($email)
    {
        $count = User::where('email', $email)->count();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkUserName($username)
    {
        $count = User::where('username', $username)->count();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function verifyEmailAddress($token)
    {
        $verifyToken = $this->verifyUser->getUserWtihToken($token);
        if (empty($verifyToken)) {
            return false;
        }
        $user = User::find($verifyToken->user_id);
        if (empty($user->email_verified_at)) {
            $array = ['email_verified_at' => Carbon::now()];
            $this->verifyUser->deleteTokenById($verifyToken->id);
            User::with('id', $verifyToken)->update($array);
        }

        $arr = array(
            'status' => 1
        );
        User::where('id', $verifyToken->user_id)->update($arr);
        return true;
    }

    public function login($data)
    {
        $rememberMe = false;
        if (isset($data['remember_me'])) {
            $rememberMe = true;
        }
        $auth = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $rememberMe);
        if ($auth) {
            return true;
        } else {
            return false;
        }
    }

    public function forgetPassword($email)
    {
        $user = User::where('email', $email);
        if ($user->count() == 1) {
            $token = $this->passwordReset->save($email);
            Mail::to($email)->send(new ForgetPassword($user->first(), $token));
            return true;
        } else {
            return false;
        }
    }

    public function verifyForgetPasswordToken($token)
    {
        $verifyToken = $this->passwordReset->getUserByToken($token);
        if (empty($verifyToken)) {
            return false;
        }
        return $token;
    }

    public function setNewPassword($data)
    {
        $email = $this->passwordReset->deleteToken($data['token']);
        if (empty($email->email)) {
            return false;
        }
        $u = User::where('email', $email->email)->update(['password' => Hash::make($data['password'])]);
        if ($u) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUserOnDashboard($data)
    {
        $arr = array(
            'name' => $data['name'],
            'phone' => $data['phone'],
        );
        if(isset($data['profile_image'])){
            $image = $data['profile_image']->store('Profile');
            $arr = $arr + ['profile_image' => $image];
            Auth::user()->profile_image = $image;
        }

        User::where('id', $data['id'])->update($arr);
        return true;

    }

    public function checkEmailInvition($email)
    {
        $email = User::where('email', $email)->get();
        $data = "";
        if (count($email) > 0) {
            $data = $email;
            return $data;
        } else {

            return $data;
        }
    }

    public function saveInvitionStudent($data)
    {
        $password = substr(uniqid(rand(), true), 0, 10);
        $id = Crypt::decrypt($data['user_id']);
        $course_id = Crypt::decrypt($data['course_id']);
        $lpo_request_id = Crypt::decrypt($data['request_id']);
        $course_name=$data['course_name'];
        $Users = new User();
        $Users->name = $data['name'];
        $Users->email = $data['email'];
        $Users->parent_id = $id;
        $Users->password = Hash::make($password);
        $Users->role_id = 3;
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        if ($Users->save()) {
            $EnrolledStudents = new EnrolledStudents();
            $EnrolledStudents->course_id = $course_id;
            $EnrolledStudents->user_id = $Users->id;
            $EnrolledStudents->lpo_request_id = $lpo_request_id;
            $EnrolledStudents->expiring_at = Carbon::now();
            $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
            if ($EnrolledStudents->save()) {
                $this->verifyUser->save($Users->id);
                Mail::to($data['email'])->send(new InviteVerification($Users->id,$password,$course_name));
                return true;
            } else {
                return false;

            }
        }
    }
    public function saveInvitionenrolledStudents($data,$user_id,$username){
        $course_id = Crypt::decrypt($data['course_id']);
        $lpo_request_id = Crypt::decrypt($data['request_id']);
        $course_name=$data['course_name'];
        $EnrolledStudents = new EnrolledStudents();
        $EnrolledStudents->course_id = $course_id;
        $EnrolledStudents->user_id = $user_id;
        $EnrolledStudents->lpo_request_id = $lpo_request_id;
        $EnrolledStudents->expiring_at = Carbon::now();
        $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
        if ($EnrolledStudents->save()) {
            Mail::to($data['email'])->send(new InviteToCourse($username,$course_name));
            return true;
        } else {
            return false;

        }


    }
    public function saveInvitionStudentByImport($lpo_request_id,$course_id,$course_name,$email,$name)
    {
        $password = substr(uniqid(rand(), true), 0, 10);
        $lpo_request_id = Crypt::decrypt($lpo_request_id);
        $Users = new User();
        $Users->name = $name??"trainee";
        $Users->email = $email;
        $Users->parent_id = Auth::user()->id;
        $Users->password = Hash::make($password);
        $Users->role_id = 3;
        $Users->remember_token = substr(md5(uniqid(rand(), true)), 0, 10);
        if ($Users->save()) {
            $EnrolledStudents = new EnrolledStudents();
            $EnrolledStudents->course_id = $course_id;
            $EnrolledStudents->user_id = $Users->id;
            $EnrolledStudents->lpo_request_id = $lpo_request_id;
            $EnrolledStudents->expiring_at = Carbon::now();
            $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
            if ($EnrolledStudents->save()) {
                $this->verifyUser->save($Users->id);
                Mail::to($email)->send(new InviteVerification($Users->id,$password,$course_name));
                return true;
            } else {
                return false;

            }
        }
    }
    public function saveInvitionenrolledStudentsByImport($lpo_request_id,$course_id,$course_name,$user_id,$username,$email){
        $lpo_request_id = Crypt::decrypt($lpo_request_id);
        $EnrolledStudents = new EnrolledStudents();
        $EnrolledStudents->course_id = $course_id;
        $EnrolledStudents->user_id = $user_id;
        $EnrolledStudents->lpo_request_id = $lpo_request_id;
        $EnrolledStudents->expiring_at = Carbon::now();
        $EnrolledStudents->active = EnrolledStudents::$ACTIVE;
        if ($EnrolledStudents->save()) {
            Mail::to($email)->send(new InviteToCourse($username,$course_name));
            return true;
        } else {
            return false;

        }

    }
    public function checkEnrollesInvition($request_id,$user_id)
    {
        $request_id = Crypt::decrypt($request_id);
        $enrolled = EnrolledStudents::where('user_id', $user_id)->where('lpo_request_id', $request_id)->get();
        $data = 0;
        if (count($enrolled) > 0) {
            $data = 1;
            return $data;
        } else {

            return $data;
        }
    }

    public function inActiveUser($id){
        $id = Crypt::decrypt($id);
        return User::where('id', $id)->update(['status' => 0]);
    }

    public function ActiveUser($id){
        $id = Crypt::decrypt($id);
        return User::where('id', $id)->update(['status' => 1]);
    }

    public function getCountryCandites()
    {
         return User::groupBy('country')->get();

         //$tasks->groupBy('category_id')->map->count();

        /* $user_info = DB::table('usermetas')
                 ->select('browser', DB::raw('count(*) as total'))
                 ->groupBy('browser')
                 ->get();*/

    //}

}
