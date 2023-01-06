<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController {
    public static function index (Router $router) {
        session_start();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);



        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto (Router $router) {
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto =  new Proyecto($_POST);

            // Validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // Generar una URL unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                
                // Guardar el Proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }


        $router->render('dashboard/crear-proyecto', [
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]);
    }

    public static function proyecto (Router $router) {
        session_start();
        isAuth();

        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil (Router $router) {
        session_start();
        isAuth();

        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPerfil();

            if(empty($alertas)) {
                //
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Mostrar mensaje de error
                    Usuario::setAlerta('error', 'El email ya se encuentra en uso');
                } else {
                // Guardar el usuario
                $usuario->guardar();
                Usuario::setAlerta('exito', 'Guardado Correctamente');

                // Asignar el nombre nuevo a la Barra
                $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            //Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if($resultado) {
                    $usuario->password = $usuario->password_nuevo;

                    // Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el Nuevo Password
                    $usuario->hashPassword();

                    // Actualizar en la BD
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Password Actualizado correctamente');
                        $alertas = $usuario->getAlertas();
                    }

                } else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }

    public static function eliminar_proyecto() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $proyecto = Proyecto::find($id);
            $proyecto->eliminar();
            
            // Redireccionar
            header('Location: /dashboard');        
        }
    }

    /* public static function eliminar_proyecto() {
 
        session_start();
        isAuth();
 
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $proyecto = Proyecto::where('url', $_POST['id']);
            
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al borrar el proyecto'
                ];
                
                echo json_encode($respuesta);
                return;
            }
 
            $resultado = $proyecto->eliminar();
            
            if($resultado) {
                $respuesta = [
                    'resultado' => $resultado,
                    'mensaje' => 'Proyecto eliminado correctamente',
                    'tipo' => 'exito',
                    'proyectoId' => $proyecto->id
                ];
                
                echo json_encode(['respuesta' => $respuesta]);
            }
        }
    } */


}