<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: log.html");
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($conexion, "SELECT id, nombre, rol, password FROM usuarios WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Acepta contraseña con hash (recomendado) o texto plano (como está hoy en tu BD)
$valida = $usuario && (
    password_verify($password, $usuario['password']) ||
    hash_equals($usuario['password'], $password)
);

$destinos = ['docente' => 'docente.php', 'soporte' => 'soporte.php'];

if ($valida && isset($destinos[$usuario['rol']])) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre']     = $usuario['nombre'];
    $_SESSION['rol']        = $usuario['rol'];
    header("Location: " . $destinos[$usuario['rol']]);
    exit();
}

header("Location: log.html?error=1");
exit();