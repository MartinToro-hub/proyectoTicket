<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'docente') {
    header("Location: log.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: docente.php");
    exit();
}

$categorias  = ['Hardware', 'Software', 'Red / Wi-Fi', 'Audiovisual', 'Otro'];
$prioridades = ['Baja', 'Media', 'Alta', 'Critica'];

$titulo            = trim($_POST['titulo'] ?? '');
$categoria         = $_POST['categoria'] ?? '';
$ubicacion         = trim($_POST['ubicacion'] ?? '');
$codigo_inventario = trim($_POST['codigo_inventario'] ?? '');
$prioridad         = $_POST['prioridad'] ?? 'Media';
$descripcion       = trim($_POST['descripcion'] ?? '');
$usuario_id        = (int) $_SESSION['usuario_id'];

if ($titulo === '' || $ubicacion === '' || $descripcion === ''
    || !in_array($categoria, $categorias, true)
    || !in_array($prioridad, $prioridades, true)) {
    die("Datos inválidos. <a href='docente.php'>Volver</a>");
}

$stmt = mysqli_prepare($conexion,
    "INSERT INTO tickets (titulo, categoria, ubicacion, codigo_inventario, prioridad, descripcion, usuario_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssi", $titulo, $categoria, $ubicacion, $codigo_inventario, $prioridad, $descripcion, $usuario_id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Su reporte fue enviado con éxito'); window.location.href='docente.php';</script>";
} else {
    echo "Error al guardar el ticket: " . htmlspecialchars(mysqli_error($conexion));
}