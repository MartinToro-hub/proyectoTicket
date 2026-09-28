<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: log.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General - Panel TIC</title>
</head>
<body>
    <header> 
        <h1>Sistema de Gestión TIC</h1>
        <p>Bienvenido, <?php echo $_SESSION['nombre']; ?> (<?php echo $_SESSION['rol']; ?>)</p>
        <!-- como lo visualizo yo? -->
        <!-- Enlace para cerrar sesión -->
        <a href="logout.php">
            <button>Cerrar Sesión</button>
        </a>
    </header>
</body>
</html>