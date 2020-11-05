<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function respondWithToken($token, $tarifas, $id)
    {
        return response()->json([
            'ok' => 'true',
            'operador' => $id,
            'token' => $token,
            'token_type' => 'bearer',
            'tarifas' => $tarifas,
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }

    public function crearRespuesta($datos, $codigo) {
        return response()->json(['data' => $datos, 'ok'=>true], $codigo);
    }
    public function crearRespuestaError($mensaje, $codigo){
        return response()->json(['message'=>$mensaje, 'code'=>$codigo], $codigo);
    }
    public function tiempo(){

        return Carbon::now();

    }


}
