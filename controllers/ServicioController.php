<?php 

namespace Controllers;

use Model\Servicio;
use MVC\Router;

Class ServicioController{

    public static function index(Router $router) {

        if (!isset($_SESSION)) {
            session_start();
        }

        isAdmin();

        $servicios = Servicio::all();
        
        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'servicios' => $servicios,
        ]);
    }


    public static function crear(Router $router) {
        

        if (!isset($_SESSION)) {
            session_start();
        }

        isAdmin();

        $servicio = new Servicio;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $resultado = $servicio->guardar();
                if ($resultado) {
                    header('Location: /servicios');
                } else {
                    $alertas['error'][] = 'Error al guardar el servicio';
                }
            }
        }

        

        
        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'servicio' => $servicio,
            'alertas' => $alertas,
    ]);

    }

    public static function actualizar(Router $router) {
        
        if (!isset($_SESSION)) {
            session_start();
        }

        isAdmin();

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) return;
        

        $servicio= Servicio::find($_GET['id']);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $resultado = $servicio->guardar();
                if ($resultado) {
                    header('Location: /servicios');
                } else {
                    $alertas['error'][] = 'Error al actualizar el servicio';
                }
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'servicio' => $servicio,
            'alertas' => $alertas,
        ]);
    }

    public static function eliminar() {
        if (!isset($_SESSION)) {
            session_start();
        }

        isAdmin();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $servicio = Servicio::find($id);
            $servicio->eliminar();
            header('Location: /servicios');
        }

        
    }
}

?>