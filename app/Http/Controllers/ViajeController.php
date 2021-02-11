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

    DB::insert('insert into viaje 
    (km, hora_inicio, hora_termino, precio, id_chofer, usuario_creacion,
     usuario_modificacion, fecha_creacion, fecha_modificacion, activo) 
     values 
     (?,?,?,?,?,?,?,?,?,?)', 
     [$request->get('km'), $request->get('hora_inicio'), $request->get('hora_termino'),
     $request->get('precio'),  $request->get('id_chofer'), $request->get('usuario_creacion'),
     $request->get('usuario_creacion'), $this->tiempo(), $this->tiempo(), true]);


    //  $viajeId = DB::getPdo()->lastInsertId();

    // $midetalle = $ojso["detalle"];
    // foreach($midetalle as $detalle){

    //     DB::insert('insert into detalle_viaje (id_viaje, latitud, longitud, precio, usuario_creacion,
    //     usuario_modificacion, fecha_creacion, fecha_modificacion, activo)
    //     values
    //     (?,?,?,?,?,?,?,?,?)', 
    //     [$viajeId, $detalle['latitud'], $detalle['longitud'], $detalle['precio'], $detalle['usuario_creacion'],
    //     $detalle['usuario_creacion'],$this->tiempo(), $this->tiempo(), true]);
    // }
    return $this->crearRespuesta('El elemento ha sido creado', 201);

   }
   public function getViajesDiarios($id){
        $date = Carbon::now();
       $data = DB::table('viaje')
       ->select('*')
       ->where('id_chofer', $id)
       ->where('activo', true)
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
}
