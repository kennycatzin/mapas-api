<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TarifaController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function getTarifas(){
        $contador = 0;
        $config = DB::table('conf_taximetro')
        ->select("tarifa_minima", "banderazo", "intervalo_tiempo", "intervalo_distancia", "tarifa_tiempo")
        ->where("activo", true)
        ->first();

        $horarios = DB::table('horario')
        ->select("id_horario", "nombre", "hora_inicial", "hora_final")
        ->where("activo", true)
        ->get();
// hola
        foreach($horarios as $horario){
            $detalle_horario = DB::table('detalle_horario')
            ->select("orden", "precio", "km_inicial", "km_final")
            ->where("activo", true)
            ->where("id_horario", $horario->id_horario)
            ->orderBy("orden", "ASC")
            ->get();

            if($contador == 0){
                $horarios=json_decode(json_encode($horarios), true);
            }
            $horarios[$contador]+=["detalle_horario"=>$detalle_horario];
            $contador ++;
        }
        $config=json_decode(json_encode($config), true);
        $config+=["horarios"=>$horarios];


        return $config;
    }


 
  
}
