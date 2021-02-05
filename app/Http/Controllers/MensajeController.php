<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class MensajeController extends Controller
{   
    public function storeMensaje(Request $request){
        try {
            $status = 5;
            DB::insert('insert into mensaje 
            (id_origen, id_destino, id_status, id_viaje, titulo, mensaje, tipo,
            activo, fecha_creacion, fecha_modificacion, usuario_creacion,
            usuario_modificacion, orden) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?)', 
            [10, 4, $status, 0, "Adeudo de viaje", "PAga we!!", 
            "TG", true, $this->tiempo(), $this->tiempo(), 1, 1, 2]);
           return $this->crearRespuesta("mensaje guardado", 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("No se pudo almacenar ".$th->getMessage(), 300);
        }
    }
    public function getMensajes($id){

        try {
            $data = DB::table('mensaje')
            ->join('users', 'users.id', '=', 'mensaje.id_origen')
            ->join('gen_catestatus', 'gen_catestatus.EstatusID', '=', 'mensaje.id_status')
            ->select("mensaje.id_mensaje", "mensaje.titulo", "mensaje.mensaje", "mensaje.tipo", 
            "users.name", "mensaje.id_status", "gen_catestatus.Estatus")
            ->whereDate('mensaje.fecha_creacion', Carbon::today())
            ->where('mensaje.id_destino', $id)
            ->where('mensaje.activo', true)
            ->orderBy('mensaje.fecha_creacion', 'DESC')
            ->get();
            return response()->json(['mensajes' => $data, 'ok'=>true], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError('Ha ocurrido un error'. $this->getMessage(), 300);
        }

    }
    public function getDetalleMensaje($id){
        try {
            $data = DB::table('mensaje')
            ->join('users', 'users.id', '=', 'mensaje.id_origen')
            ->select('mensaje.id_mensaje', 'mensaje.titulo', 'mensaje.mensaje',
            'mensaje.tipo', 'users.name')
            ->where('mensaje.id_mensaje', $id)
            ->where('mensaje.activo', true)
            ->get();
            return response()->json(['mensaje' => $data, 'ok'=>true], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un error ". $th->getMessage(), 300);
        }
    }
    public function mensajeVisto($id){
        try {
            $id_status = 13;
            DB::update('update mensaje set id_status = ?, fecha_creacion = ? 
            where id_mensaje = ?', 
            [$id_status,  $this->tiempo(), $id]);
            return $this->crearRespuesta("Registro actualizado", 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("No se pudo almacenar ".$th->getMessage(), 300);
        }
    }
    public function getMensajesNuevos($id){
        $id_status = 5;
        $data = DB::table('mensaje')
            ->join('users', 'users.id', '=', 'mensaje.id_origen')
            ->join('gen_catestatus', 'gen_catestatus.EstatusID', '=', 'mensaje.id_status')
            ->select("mensaje.id_mensaje", "mensaje.id_viaje","mensaje.titulo", "mensaje.mensaje", "mensaje.tipo", 
            "users.name", "mensaje.id_status", "gen_catestatus.Estatus")
            ->whereDate('mensaje.fecha_creacion', Carbon::today())
            ->where('mensaje.id_destino', $id)
            ->where('mensaje.id_status', $id_status)
            ->where('mensaje.activo', true)
            ->orderBy('mensaje.orden', 'ASC')
            ->first();
            $arrVacio = array(
                "info" => "vacio"
            );
        if($data == null){
            return response()->json(['mensaje' => $arrVacio, 'ok'=>false], 200);
        }else{
            return response()->json(['mensaje' => $data, 'ok'=>true], 200);
        }
    }

}
