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
       $data = DB::table('viaje')
       ->select('*')
       ->where('id_chofer', $id)
       ->where('activo', true)
       ->whereDate('fecha_creacion', Carbon::today())
       ->get();

       $total = DB::table('viaje')
       ->select('*')
       ->where('id_chofer', $id)
       ->where('activo', true)
       ->sum('precio');

       return response()->json(['data' => $data, 'total'=> $total], 200);
   }
}
