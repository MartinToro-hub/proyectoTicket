<?php
// Inicia o reanuda la sesión del usuario para almacenar sus datos si el login es exitoso
session_start();

// Incluye el archivo de conexión a la base de datos MySQL
require 'conexion.php';

// VERIFICACIÓN DEL MÉTODO HTTP:
// Si no es una petición POST (por ejemplo, si entran directamente escribiendo la URL), redirige al login
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: log.html");
    exit();
}

// CAPTURA Y LIMPIEZA DE DATOS:
// Obtiene el email quitando espacios en blanco al inicio/final. Si no existe, asigna un string vacío
$email    = trim($_POST['email'] ?? '');
// Obtiene la contraseña ingresada en el formulario
$password = $_POST['password'] ?? '';

// CONSULTA PREPARADA (Protección contra Inyección SQL):
// Prepara la consulta para buscar id, nombre, rol y contraseña del usuario con ese email
$stmt = mysqli_prepare($conexion, "SELECT id, nombre, rol, password FROM usuarios WHERE email = ? LIMIT 1");
// Vincula el parámetro $email como string ("s") a la casilla '?' de la consulta
mysqli_stmt_bind_param($stmt, "s", $email);
// Ejecuta la consulta
mysqli_stmt_execute($stmt);
// Obtiene el resultado de la consulta como un arreglo asociativo
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// VALIDACIÓN DE CREDENCIALES:
// Verifica que el usuario exista Y que la contraseña coincida por alguna de estas dos formas:
// 1. password_verify(): Compara contra un hash seguro encriptado (método recomendado).
// 2. hash_equals(): Compara en texto plano sin sufrir ataques de temporización (para usuarios antiguos).
$valida = $usuario && (
    password_verify($password, $usuario['password']) ||
    hash_equals($usuario['password'], $password)
);

// CONTROL DE ACCESO BASADO EN ROLES (RBAC):
// Mapeo que define a qué página redirigir según el rol guardado en la base de datos
$destinos = ['docente' => 'docente.php', 'soporte' => 'soporte.php'];

// LOGIN EXITOSO:
// Si las credenciales son válidas Y el rol del usuario existe dentro del diccionario $destinos
if ($valida && isset($destinos[$usuario['rol']])) {
    // Regenera el ID de la sesión para evitar ataques de fijación de sesión (Session Fixation)
    session_regenerate_id(true);
    
    // Almacena la información clave del usuario en la sesión global
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre']     = $usuario['nombre'];
    $_SESSION['rol']        = $usuario['rol'];
    
    // Redirige al usuario a la vista correspondiente según su rol
    header("Location: " . $destinos[$usuario['rol']]);
    exit();
}

// LOGIN FALLIDO:
// Si las credenciales o el rol son inválidos, redirige al formulario con parámetro de error
header("Location: log.html?error=1");
exit();