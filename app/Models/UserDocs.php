<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserDocs extends Authenticatable
{
    use HasFactory, Notifiable;

    public static $ACTIVE = 1;
    public static $IN_ACTIVE = 0;

    public static $ADMIN = 1;
    public static $SUB_ADMIN = 2;
    public static $USER = 3;

protected $table = 'users_docs';

    public static $validation = [

        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users',
        'contact_number' => 'required',
        'password' => 'required',
        'role' => 'required',

    ];

    public static $validationUpdate = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'contact_number' => 'required',
        'role' => 'required',
    ];

    public static $userLoginRules = [
        'email' => 'required',
        'password' => 'required'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



}
