<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
        'username' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
          return [
              'status' => 0,
              'errors' => $validator->errors()
          ];
      }

      try {

        $user = User::create([
          'username' => $request->username,
          'email' => $request->email,
          'password' => Hash::make($request->password)
        ]);

        if ($user) {

          return response()->json([
            'status' => 1,
            'msg' => 'Successfully registered'
          ]);

        }

      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'Internal server error'
        ]);

      }

  }
}
