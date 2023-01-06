<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController {
    public static function login (Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario =  new Usuario($_POST);
            $alertas = $usuario->validarLogin();

           if(empty($alertas)) {
            // Comprobar que exista el usuario
            $usuario = Usuario::where('email', $usuario->email);

            if(!$usuario || !$usuario->confirmado) {
                Usuario::setAlerta('error', 'El Usuario No Existe  o no esta confirmado');
                
            } else {
                // El Usuario existe
                if(password_verify($_POST['password'], $usuario->password)) {
                    // Iniciar Sesion
                    session_start();
                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    // Redireccionar
                    header('Location: /dashboard');
                } else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                }
            }
        }
    }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout () {
        session_start();

        $_SESSION = [];
        header('Location: /');

    }

    public static function crear (Router $router) {

        $usuario =  new Usuario;
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
           
            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password 2
                    unset($usuario->password2);
                    
                    // Generar Token
                    $usuario->crearToken();

                    // Enviar el email del token
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear un Nuevo usuario
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
            
        }

         // Render a la vista
         $router->render('auth/crear', [
            'titulo' => 'Crear tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide (Router $router) {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado === '1') {
                    
                    //Generar un token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();
                    
                    //Enviar el email
                    $email =  new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                } else {
                    // Alerta de error
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer (Router $router) {
        
        $alertas = [];
        $mostrar = true;

        $token = s($_GET['token']);
        if(!$token) header('Location: /');

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Leer el nuevo password y guardarlo
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear el nuevo Password
                $usuario->hashPassword();
                
                // Eliminar el token
                $usuario->token = null;

                // Guardar nuevo Password en la BD
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado) {
                    header('Location: /');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
    public static function mensaje (Router $router) {
        
         // Render a la vista
         $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada exitosamente'
        ]);
    }

    public static function confirmar (Router $router) {
        $alertas = [];
        $token = s($_GET['token']);

        if(!$token) header('Location: /');
    
        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // No se encontro un Usuario con ese token
            Usuario::setAlerta('error', 'Token no Valido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = "1";
            $usuario->token = null;
            unset($usuario->password2);

            // Guardar en la BD
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }
    
        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas'=> $alertas
        ]);
    }
}