<?php
session_start();
// Control de acceso: requiere sesión activa y rol 'docente'
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'docente') {
    header("Location: log.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente - Crear Ticket</title>
    <link rel="stylesheet" href="docentesstyle.css">
</head>
<body>
   <header>
    <div class="header-info">
        <h1>Panel de Docentes</h1>
        <p>Bienvenido: <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>

    <!-- Este contenedor agrupa el logo y el botón directamente lado a lado -->
    <div class="header-actions">
        <img src="cftlogo.png" alt="Logo Institución" class="header-logo">
        <a href="logout.php"><button class="btn-logout">Cerrar Sesión</button></a>
    </div>
</header>

    <main>
        <h2>Crear Nuevo Reporte de Incidencia</h2>
        <form action="guardar_ticket.php" method="POST">
            <div>
                <label>Título del problema:</label><br>
                <input type="text" name="titulo" required placeholder="Ej: Proyector no enciende">
            </div><br>

            <div>
                <label>Categoría:</label><br>
                <select name="categoria" required>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                    <option value="Red / Wi-Fi">Red / Wi-Fi</option>
                    <option value="Audiovisual">Audiovisual</option>
                    <option value="Otro">Otro</option>
                </select>
            </div><br>

            <div>
                <label>Ubicación (Sala / Laboratorio):</label><br>
                <input type="text" name="ubicacion" required placeholder="Ej: Laboratorio 2">
            </div><br>

            <div>
                <label>Código de Inventario (Opcional):</label><br>
                <input type="text" name="codigo_inventario" placeholder="Ej: PC-LAB2-05">
            </div><br>

            <div>
                <label>Prioridad:</label><br>
                <select name="prioridad">
                    <option value="Baja">Baja</option>
                    <option value="Media" selected>Media</option>
                    <option value="Alta">Alta</option>
                    <option value="Critica">Crítica</option>
                </select>
            </div><br>

            <div>
                <label>Descripción detallada:</label><br>
                <textarea name="descripcion" rows="4" required placeholder="Describe la falla..."></textarea>
            </div><br>

            <button type="submit">Enviar Reporte</button>
        </form>
    </main>
</body>
</html>