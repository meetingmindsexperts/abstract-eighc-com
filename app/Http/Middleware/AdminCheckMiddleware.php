<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $segments = $request->segments();
        if(in_array('admin',$segments)){
          if(in_array('en',$segments)){
              if(in_array('login',$segments)){
                return $next($request);
              }
            if(!Auth::user()){
                \Session::flash('error', 'Please Login...!');
                return redirect()->to(route('admin.admin-login'));
            }
            if(Auth::user() && Auth::user()->role != 1){
                \Session::flash('error', 'Please Login...!');
                return redirect()->to(route('admin.admin-login'));
            }
          }
        }
        return $next($request);
    }
}
