<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Hash;
use Socialite;
use App\Models\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback_facebook()
    {
        try {

            $user = Socialite::driver('facebook')->stateless()->user();

            //disini data user bisa diregister atau login jika sudah pernah register.
            //sekarang belum bisa karena masih di localhost

            return response()->json([
              'status' => 1,
              'msg' => 'get data success',
              'data' => $user
            ]);

        } catch (Exception $exception) {
          return response()->json([
            'status' => 0,
            'msg' => 'error',
          ]);
        }
    }

    public function google()
    {
      return Socialite::driver('google')->redirect();
    }

    public function callback_google()
   {
       try {

           $user = Socialite::driver('google')->user();

           //disini data user bisa diregister atau login jika sudah pernah register.
           //sekarang belum bisa karena masih di localhost

           return response()->json([
             'status' => 1,
             'msg' => 'get data success',
             'data' => $user
           ]);


       } catch (Exception $e) {
         return response()->json([
           'status' => 0,
           'msg' => 'error',
         ]);
       }
   }
}
