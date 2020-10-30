<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = DB::table('users')
        ->select('id', 'activo')
        ->where('email', $request['email'])
        ->first();
        if($data != null){

      
       
            


        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['ok' => 'no','mensaje' => 'Acceso incorrecto'], 401);
        }
        if(!$data->activo){
            return response()->json(['ok' => 'pago', 'mensaje' => "Favor de pagar!!!"], 200);
        }

        return $this->respondWithToken($token, $data->id);
    }else{
        return response()->json(['ok'=>'null', 'mensaje' => 'Usuario no encontrado'], 401);
    }
    }


}