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
            (id_origen, id_destino, id_status, titulo, mensaje, tipo,
            activo, fecha_creacion, fecha_modificacion, usuario_creacion,
            usuario_modificacion) 
            values (?,?,?,?,?,?,?,?,?,?,?)', 
            [10, 4, $status, "Mensajed de Call Center", "Nuevo viaje para Calle 14 sin numero 25a y 25 b colonia benito juarez oriente
            casa roja, rejas negras, coche en la puerta", 2, true, "2021-02-03", "2021-02-03", 1, 1]);
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
            ->orderBy('mensaje.fecha_creacion', 'ASC')
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
}
