<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: log.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
    $nuevo_estado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_SPECIAL_CHARS);
    $origen = filter_input(INPUT_POST, 'origen', FILTER_SANITIZE_SPECIAL_CHARS);

    $estados_permitidos = ['Resuelto', 'Completado', 'Abierto', 'En Proceso'];

    if ($ticket_id && in_array($nuevo_estado, $estados_permitidos, true)) {
        $stmt = mysqli_prepare($conexion, "UPDATE tickets SET estado = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $ticket_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            
            // Redirección dinámica según origen o nuevo estado
            if ($origen === 'completados' && $nuevo_estado === 'Completado') {
                header("Location: completados.php?msg=actualizado");
            } else {
                header("Location: soporte.php?msg=actualizado");
            }
            exit();
        } else {
            echo "Error al actualizar el estado: " . mysqli_error($conexion);
        }
    } else {
        header("Location: soporte.php?error=datos_invalidos");
        exit();
    }
} else {
    header("Location: soporte.php");
    exit();
}