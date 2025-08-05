<?php 


namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController{

    public static function index() {


        $servicios = Servicio::all();

        echo json_encode($servicios); // para ver en .JSON
    }


    public static function guardar() {

        // almacena la cita y devuelve el resultado

        $cita = new Cita($_POST);

        $resultado = $cita->guardar();

        $idCita = $resultado['id']; //resultado es el id de la cita 

        // almacena la cita y el servicio

        $idServicios = explode(",", $_POST['servicios']);

        

        foreach($idServicios as $idServicio) {
            $args = [
                'citasId' => $idCita,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        echo json_encode(['resultado' => $resultado]);
    }


    public static function eliminar(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $cita = Cita::find($id);
            $cita->eliminar();
            
            header('Location:' . $_SERVER['HTTP_REFERER']); // LA PAGINA DE LA QUE VENIAMOS
        }
    }

}
?>