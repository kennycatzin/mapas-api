<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TarifaController as tarifa;


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
            'password' => 'required|confirmed',
            'usuario' => 'required|unique:users'
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->usuario = $request->input('usuario');
            $user->imagen = $request->input('imagen');
            $user->id_operador = $request->input('id_operador');
            $user->activo = true;
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
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);
        //probando para subir


        $data = DB::table('users')
        ->join('vw_catoperadores', 'vw_catoperadores.OperadorID', '=', 'users.id_operador')
        ->select('users.id_operador', 'vw_catoperadores.OperadorID', 'users.id', 'users.activo', 'users.imagen', 'vw_catoperadores.NumEconomico', 
        'vw_catoperadores.TituloSindical', DB::raw("CONCAT(vw_catoperadores.Nombre,' ',vw_catoperadores.ApellidoPaterno) AS nombre"))
        ->where('users.usuario', $request['usuario'])
        ->first();
        if($data != null){
        $credentials = $request->only(['usuario', 'password']);
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['ok' => 'no','mensaje' => 'Acceso incorrecto'], 401);
        }
        if(!$data->activo){
            return response()->json(['ok' => 'pago', 'mensaje' => "Favor de pagar!!!"], 200);
        }
        $tarifaController = new tarifa();

        $tarifas = $tarifaController->getTarifas();
        $data->imagen = getenv("RUTA_FOTOS", "");
        $data->imagen = $data->imagen.'/F'.$data->OperadorID.".jpg";

        return $this->respondWithToken($token, $tarifas, $data);
    }else{
        return response()->json(['ok'=>'null', 'mensaje' => 'Usuario no encontrado'], 401);
    }
    }


}