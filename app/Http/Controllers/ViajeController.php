<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ViajeController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


   public function guardarViaje(Request $request){
    $json = json_encode($request->input());
    $ojso = json_decode($json, true);
    $tiempo_espera=0;
    $precio_tiempo_espera=0;
    $iva=0;
    $comision=0;
    $comision_total=0;
    $id_status=8;

    if($request->get('tipo_viaje') == 2){
        $comision= $request->get('precio') * .029;
        $comision_total = $comision + 2.5;
        $comision_total = round($comision_total, 2, PHP_ROUND_HALF_UP);
        $iva=$comision_total * 0.16;
        $iva = round($iva, 2, PHP_ROUND_HALF_UP);
        $comision_total = $comision_total + $iva;
        $id_status=11;
    }

    DB::insert('insert into viaje 
    (km, hora_inicio, hora_termino, precio, id_chofer, usuario_creacion,
     usuario_modificacion, fecha_creacion, fecha_modificacion, activo,
     tiempo_espera, precio_tiempo_espera, tipo_viaje, iva, comision, 
     comision_total, id_status) 
     values 
     (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
     [$request->get('km'), $request->get('hora_inicio'), $request->get('hora_termino'),
     $request->get('precio'),  $request->get('id_chofer'), $request->get('usuario_creacion'),
     $request->get('usuario_creacion'), $this->tiempo(), $this->tiempo(), true,
     $tiempo_espera, $request->get('precio_tiempo_espera'), $request->get('tipo_viaje'), $iva, 
     $comision, $comision_total, $id_status]);


    return response()->json(['data' => "el elementro ha sido creado", 'id_viaje'=>DB::getPdo()->lastInsertId(), 'ok'=>true], 200);



    return $this->crearRespuesta('El elemento ha sido creado', 201);

   }
   public function getViajesDiarios($id){
        $date = Carbon::now('America/Mexico_City');
        $id_pagado = 8;
       $data = DB::table('viaje')
       ->select('*')
       ->where('id_chofer', $id)
       ->where('activo', true)
       ->where('id_status', $id_pagado)
       ->whereBetween('fecha_creacion', [$date->format("Y-m-d")." 00:00:00",$date->format("Y-m-d")." 23:59:59"])
       ->orderBy('fecha_creacion', 'DESC')
       ->get();

       $total = DB::table('viaje')
       ->select('*')
       ->where('id_chofer', $id)
       ->where('activo', true)
       ->whereDate('fecha_creacion', Carbon::today())
       ->sum('precio');

       return response()->json(['data' => $data, 'total'=> $total], 200);
   }
   public function aceptarViaje($id_viaje){
       $id_asignado = 12;
        DB::update('update cc_asignacionviajes
         set fecha = ?, estatus_id = ? 
         where id = ?', 
         [$this->tiempo(), $id_asignado, $id_viaje]);
         return $this->crearRespuesta("Se ha asignado el viaje correctamente", 200);    
    }
    public function rechazarViaje($id_viaje){
        $id_rechazado= 14;
        $id_activo = 5;
        DB::update('update cc_asignacionviajes
        set fecha = ?, estatus_id = ? 
        where id = ?', 
        [$this->tiempo(), $id_rechazado, $id_viaje]);

        $data= DB::table('cc_asignacionviajes')
        ->select('viajes_id')
        ->where('id', $id_viaje)
        ->first();

        DB::update('update cc_viajes set 
        fecha = ?, estatus_id = ? 
        where id = ?', 
        [$this->tiempo(), $id_activo, $data->viajes_id]);
        return $this->crearRespuesta("Se ha rechazado el viaje", 200);    
    }
    public function getUltimoViaje($id_viaje){
        $data=DB::table('viaje')
        ->select('*')
        ->where('id_viaje', $id_viaje)
        ->where('activo', 1)
        ->where('tipo_viaje', 2)
        ->where('id_status', 11)
        ->orderBy('id_viaje', 'DESC')
        ->first();

        $userKey=DB::table('users')
        ->select('public_key')
        ->where('id', $data->id_chofer)
        ->first();
        return response()->json(['data' => $data, 'clave'=>$userKey, 'ok'=>true], 200);

    }
    public function getEstatusPago($id_viaje){

        $data=DB::table('viaje')
        ->join('gen_catestatus',  'gen_catestatus.EstatusID', '=', 'viaje.id_status')
        ->select('gen_catestatus.Estatus')
        ->where('viaje.activo', 1)
        ->where('viaje.tipo_viaje', 2)
        ->where('viaje.id_viaje', $id_viaje)
        ->orderBy('viaje.id_viaje', 'DESC')
        ->first();    
        return $this->crearRespuesta($data, 200);

    }
}
