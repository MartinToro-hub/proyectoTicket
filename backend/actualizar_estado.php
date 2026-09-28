<?php
//Session Start se usa para acceder a las variables de sesion
//como el usuario que inicio sesion 
session_start();
require 'conexion.php';
//se verifica que exista $_SESSION['usuario_id'], para que haya un usuario verificado
//su rol exacto es de soporte, en caso de fallar alguna de las condiciones
//se redirige a log.html y exit(); detiene la ejecucion del archivo 
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: log.html");
    exit();
}
//aqui if "($_SERVER['REQUEST_METHOD'] === 'POST') {" hace que la pagina se pregunte
//si recibio informacion mediante el metodo 'POST', de lo contrario se ejecuta la parte final
//y es redirigido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //aqui PHP obtiene tres datos "$ticket_id" identifica el ticket a modificar
    //"$nuevo_estado" es el estado que se le asignara al ticket
    //"$origen" es la pagina de origen de la solicitud, para redirigir al usuario a la pagina correcta
    $ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
    $nuevo_estado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_SPECIAL_CHARS);
    $origen = filter_input(INPUT_POST, 'origen', FILTER_SANITIZE_SPECIAL_CHARS);
    //esto define los estados que se permiten y evita que se asigen cualquier estado
    //no definido en la base de datos, evitando inconsistencias
    $estados_permitidos = ['Resuelto', 'Completado', 'Abierto', 'En Proceso'];
    //aqui se comprueba que haya un ticket valido con "$ticket_id"
    //con "in_array($nuevo_estado, $estados_permitidos, true)" se comprueba que el estado sea permitido
    if ($ticket_id && in_array($nuevo_estado, $estados_permitidos, true)) {
        //es aqui donde realmente se modifica la base de datos
        //busca el ticket cuyo id corresponda y cambia su estado
        //los "?" son parametros que se colocaran luego
        $stmt = mysqli_prepare($conexion, "UPDATE tickets SET estado = ? WHERE id = ?");
        //aqui se asiga un valor a los "?"
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $ticket_id);
        //aqui se intenta ejecutar el update, si funciona se cierra la consulta preparada
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            
            // Redirección dinámica según origen o nuevo estado
            if ($origen === 'completados' && $nuevo_estado === 'Completado') {
                header("Location: completados.php?msg=actualizado");
            } else {
                //en caso de error se muestra un mensaje de error
                header("Location: soporte.php?msg=actualizado");
            }
            exit();
        } else {
            echo "Error al actualizar el estado: " . mysqli_error($conexion);
        }
    //si alguien accede directamente al PHP el sistema lo envia a soporte.php 
    } else {
        header("Location: soporte.php?error=datos_invalidos");
        exit();
    }
} else {
    header("Location: soporte.php");
    exit();
}