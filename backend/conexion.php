<?php
$host = "localhost";
$usuario = "root";
$password = "";
$database = "gestor_ti";

mysqli_report(MYSQLI_REPORT_OFF);
$conexion = mysqli_connect($host, $usuario, $password, $database);

if (!$conexion) {
    die("Error al conectar con la base de datos.");
}
mysqli_set_charset($conexion, "utf8mb4");