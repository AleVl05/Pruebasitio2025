<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {

        $alertas = [];
        $auth = new Usuario();


        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //comprovar que existe el usuario

                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    //verificar passç

                    if($usuario->comprobarpasswordandverificado($auth->password)){

                        //cerrar session anterior 

                        session_unset();

                        //autenticar SESION
                        
                        if(!isset($_SESSION)) //Si no hay una session iniciada
                        { 
                            session_start(); //inicia una
                        } 


                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        

                        // Redireccionamiento

                        if($usuario->admin === "1"){

                            $_SESSION['admin'] = $usuario->admin ?? null; 
                            header('Location: /admin');


                            
                        }else{

                            header('Location: /cita');

                        }

                        debuguear($_SESSION);


                        
                    }

                    




                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }

        
            $alertas = Usuario::getAlertas();

            

        }
        
        //esto es genial, render basicamente dice donde esta la pagina que quieres cargar, y dice views/LOQUELEPONGAS/NOMBREARCHIVO/.php
        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth' => $auth,
        ]);
    }


    public static function logout() {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION = []; // cerrar la session

        header('Location: /');
    }

    
    public static function olvide(Router $router) {
        
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){
                   //usuario existe 
                   //generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //TODO:enviar email

                    $email = new Email($usuario->email,$usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito','revisa tu email');

                } else {
                    //no existe
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                    
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/olvide', [
            'alertas' => $alertas,
        ]);
    }


    public static function recuperar(Router $router) {
        
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        

        $usuario = Usuario::where('token', $token);
        if(empty($usuario)){

            Usuario::setAlerta('error','Token no valido');
            $error = true;
        }
        else
        {

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // leer password y guardarlo

            $passwordNuevo = new Usuario($_POST);
            $aletas = $passwordNuevo->validarPassword();

            if(empty($alertas)){
                $usuario->password = null; //elimina el password del usuario

                $usuario->password = $passwordNuevo->password;
                $usuario->hashPassword();
                $usuario->token = '';

                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /');
                }
                
                debuguear($usuario);
            }

        }


            

        }
        
        
        



        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error,
        ]);
    }


    public static function crear(Router $router) {

        $usuario = new Usuario($_POST);

        $alertas = [];
        //ESTO sirve para verificar lo que llene en el formulario la persona
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                

                $resultado = $usuario->existeUsuario();

                //crear una mini session para guardar el email de la persona en el mensaje
                session_unset(); //cierra la anterior

                if (!isset($_SESSION)) {
                    session_start();
                }

                $_SESSION['email'] = $usuario->email;

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                } else{
                    //hasear password
                    $usuario->hashPassword();

                    //genera token

                    $usuario->crearToken();

                    //enviar el email

                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    //crear usuario

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        //PARA REDIRECCIONAR
                        header('Location: /mensaje');
                    }


                    // debuguear($usuario);
                }
            }

        }
        
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas,
        ]);

    }


    public static function mensaje(Router $router){
 

        if (!isset($_SESSION)) {
            session_start();
        }

        $emailDelUsuario = $_SESSION['email'] ?? 'tu email';

        

        $router->render('auth/mensaje', [
            'email' => $emailDelUsuario,
        ]);
    }


    public static function confirmar(Router $router){

        $alertas = [];

        $token = s($_GET['token']); //busca en la URL(get) la parte token

        //puedes usar la funcion directo de active record con ::

        $usuario = Usuario::where('token', $token); // te retorna el usuario con el token que le pidas

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no valido');
        }else{
            

            $usuario->confirmado = "1";
            $usuario->token = '';
            $usuario->guardar(); //actualiza si ya existe el id
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente!');

        }


        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas,
        ]);
    }

}

?>