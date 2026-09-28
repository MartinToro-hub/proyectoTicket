<?php
session_start();
require 'conexion.php';
//toma el archivo conexion para poder iniciar la sesion conectandose a la base de datos

//Aca esta al escucha para poder logear con el usuario que esta con el rol de  docente
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'docente') {
    header("Location: log.html");
    exit();//se detiene la ejecucion y lo redirige al incio de sesion
}

//Esto solo procesa peticiones mediante POST cuando se presiona el boton de un formulario
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: docente.php");
    exit();//Si alguien intecta acceder dese la barra de navegacion(GET) lo redirige a docente.php
}
//Definicion de opciones validas (listas blancas) para validar los datos recibidos
$categorias  = ['Hardware', 'Software', 'Red / Wi-Fi', 'Audiovisual', 'Otro'];
$prioridades = ['Baja', 'Media', 'Alta', 'Critica'];
//Captura y limpieza de los datos mediante el formulario del docente
// - null coalescing operator (??) evita advertencias de indice no definido si esque nuestro campo esta vacio
// - trim() Elimina espacios en blanco al inicio y alfinal del texto

$titulo            = trim($_POST['titulo'] ?? '');
$categoria         = $_POST['categoria'] ?? '';
$ubicacion         = trim($_POST['ubicacion'] ?? '');
$codigo_inventario = trim($_POST['codigo_inventario'] ?? '');
$prioridad         = $_POST['prioridad'] ?? 'Media';//Asigna media por defecto si esque no lo asigna
$descripcion       = trim($_POST['descripcion'] ?? '');

// Obtiene el ID del usuario desde la sesion activa y lo fuerza a tipo entero
$usuario_id        = (int) $_SESSION['usuario_id'];

//Comprueba que los campos obligatorios como titulo, ubicacion, descripcion no esten vacios
if ($titulo === '' || $ubicacion === '' || $descripcion === ''
    || !in_array($categoria, $categorias, true)
    || !in_array($prioridad, $prioridades, true)) {
    die("Datos inválidos. <a href='docente.php'>Volver</a>");
} //Inarray se encarga de realizar comprobacion para asegurar que la categoria y prioridad 
//pertenezcan a las listas permitidas evitando la insercion de datos alternados desde el navegador


//mysqli_prepare() compila la estructura de las consultas SQL utilizando marcadores de posicion
//mysqli_stmt_bind_param(): Asocia las variables PHP a los marcadores
//El string "ssssssi" especifica los tipos de datos de los 7 parámetros: 6 cadenas de texto (s) 
//y 1 entero (i para el usuario_id). Esto neutraliza cualquier intento de inyección SQL.
$stmt = mysqli_prepare($conexion,
    "INSERT INTO tickets (titulo, categoria, ubicacion, codigo_inventario, prioridad, descripcion, usuario_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssi", $titulo, $categoria, $ubicacion, $codigo_inventario, $prioridad, $descripcion, $usuario_id);


//myqli_stmt_execute ejecuta la insercion del formulario en la base de datos
//Exito: muestra alerta tipo js avisando que el ticket se registro correctamente y redirige a docente
//error: Imprime el mensaje de error retornado por mysql protegido htmlspecialchars para evitar vulnerabilidades xss
if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Su reporte fue enviado con éxito'); window.location.href='docente.php';</script>";
} else {
    echo "Error al guardar el ticket: " . htmlspecialchars(mysqli_error($conexion));
}