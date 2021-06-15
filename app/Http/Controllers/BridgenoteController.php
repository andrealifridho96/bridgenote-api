<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Members;
use App\Models\Clients;

class BridgenoteController extends Controller
{

    public function token($string, $action)
    {
      $encrypt_method = "AES-256-CBC";
      $secret_key = 'yVprkTQ8JS5d65hpnLrMwweNn89NdkdD';
      $secret_iv = '5cfDHNj3VVfx';
      $key = hash('sha256', $secret_key);
      $iv = substr(hash('sha256', $secret_iv), 0, 16);
      if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
      }else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
      }
      return $output;
    }

    public function RegisterClient(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'username' => 'required|unique:clients',
          'email' => 'required|email|unique:clients',
      ]);

      if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        }

      try {

        $token = $this->token($request->email, 'encrypt');

        $insert = clients::create([
          'username' => $request->username,
          'email' => $request->email,
          'token' => $token
        ]);

        if ($insert) {

          $getClient = clients::where('email', $request->email)->first();

          return response()->json([
            'status' => 1,
            'msg' => 'register success',
            'data' => [
              'client_id' => $getClient->id,
              'cliant_token' => $getClient->token,
              'username' => $getClient->username,
              'email' => $getClient->email,
            ]
          ]);

        } else {

          return response()->json([
            'status' => 0,
            'msg' => 'register failed'
          ]);

        }

      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'internal server error'
        ]);

      }

    }

    public function insert(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'status' => 'required',
          'position' => 'required',
      ]);

      if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        }

      try {

        if ($request->hasHeader('X-Client-Token')) {

          $token = $request->header('X-Client-Token');

          $getClient = clients::where('token', $token)->first();

          if ($getClient) {
            $insert = Members::create($request->all());

            if ($insert) {

              return response()->json([
                'status' => 1,
                'msg' => 'insert success'
              ]);
            } else {
              return response()->json([
                'status' => 0,
                'msg' => 'insert failed'
              ]);
            }

          } else {

            return response()->json([
              'status' => 0,
              'msg' => 'Token not found'
            ], 401);

          }

        } else {

          return response()->json([
            'status' => 0,
            'msg' => 'Token required'
          ], 401);

        }

      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'internal server error'
        ]);

      }

    }

    public function get(Request $request)
    {
      try {

        if ($request->hasHeader('X-Client-Token')) {

          $token = $request->header('X-Client-Token');

          $getClient = clients::where('token', $token)->first();

          if ($getClient) {

            if ($request->id) {

              $getMembers = Members::where('id', $request->id)->first();

              if (!$getMembers) {

                return response()->json([
                  'status' => 0,
                  'msg' => 'data not found'
                ]);

              } else {

                return response()->json([
                  'status' => 1,
                  'msg' => 'data found',
                  'data' => [
                    'id' => $getMembers->id,
                    'status' => $getMembers->status,
                    'position' => $getMembers->position,
                  ]
                ]);

                }
            } else {
              $Members = Members::all('id', 'status', 'position');

                return response()->json([
                  'status' => 1,
                  'msg' => 'data found',
                  'data' => $Members
                ]);
            }

          } else {

            return response()->json([
              'status' => 0,
              'msg' => 'Token not found'
            ], 401);

          }

        } else {

          return response()->json([
            'status' => 0,
            'msg' => 'Token required'
          ], 401);

        }

      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'internal server error'
        ]);

      }

    }

    public function update(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'id' => 'required'
      ]);

      if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        }

      try {

        if ($request->hasHeader('X-Client-Token')) {

          $token = $request->header('X-Client-Token');

          $getClient = clients::where('token', $token)->first();

          if ($getClient) {

            if ($request->status && $request->position) {
              Members::find($request->id)->update(['status' => $request->status], ['position' => $request->position]);
              return response()->json([
                'status' => 1,
                'msg' => 'data updated'
              ]);
            } elseif ($request->status) {
              Members::find($request->id)->update(['status' => $request->status]);
              return response()->json([
                'status' => 1,
                'msg' => 'data updated'
              ]);
            } elseif ($request->position) {
              Members::find($request->id)->update(['position' => $request->position]);
              return response()->json([
                'status' => 1,
                'msg' => 'data updated'
              ]);
            } else {
              return response()->json([
                'status' => 0,
                'msg' => 'no data updated'
              ]);
            }

          } else {

            return response()->json([
              'status' => 0,
              'msg' => 'Token not found'
            ], 401);

          }

        } else {

          return response()->json([
            'status' => 0,
            'msg' => 'Token required'
          ], 401);

        }


      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'internal server error'
        ]);

      }

    }

    public function delete(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'id' => 'required',
      ]);

      if ($validator->fails()) {
            return [
                'status' => 0,
                'errors' => $validator->errors()
            ];
        }


      try {

        if ($request->hasHeader('X-Client-Token')) {

          $token = $request->header('X-Client-Token');

          $getClient = clients::where('token', $token)->first();

          if ($getClient) {

            $delete = Members::where('id',$request->id)->delete();

            if ($delete) {
              return response()->json([
                'status' => 1,
                'msg' => 'data deleted'
              ]);
            } else {
              return response()->json([
                'status' => 0,
                'msg' => 'data delete failed'
              ]);
            }

          } else {

            return response()->json([
              'status' => 0,
              'msg' => 'Token not found'
            ], 401);

          }

        } else {

          return response()->json([
            'status' => 0,
            'msg' => 'Token required'
          ], 401);

        }


      } catch (Exception $e) {

        return response()->json([
          'status' => 500,
          'msg' => 'internal server error'
        ]);

      }

    }
}
