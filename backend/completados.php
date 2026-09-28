<?php
session_start();
require 'conexion.php';

// Control de acceso
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: log.html");
    exit();
}

// Traemos ÚNICAMENTE los tickets que están completados
$query = "SELECT tickets.*, usuarios.nombre AS solicitante
          FROM tickets
          JOIN usuarios ON tickets.usuario_id = usuarios.id
          WHERE tickets.estado = 'Completado'
          ORDER BY tickets.fecha_creacion DESC";

$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Tickets Completados</title>
    <link rel="stylesheet" href="soportestyle.css">
</head>
<body>
    <header class="header-soporte">
        <div class="header-brand">
            <h1>Historial de Incidencias</h1>
            <p>Tickets Resueltos y Finalizados</p>
        </div>
        <div class="header-actions">
            <a href="soporte.php" class="btn-nav">⬅ Volver a Tickets Activos</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-soporte">
        <h2>Listado de Tickets Completados</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Ubicación</th>
                        <th>Solicitante</th>
                        <th>Estado</th>
                        <th>Acción / Reabrir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($ticket = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['categoria']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['ubicacion']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['solicitante']); ?></td>
                            <td>
                                <span class="badge estado-completado">
                                    <?php echo htmlspecialchars($ticket['estado']); ?>
                                </span>
                            </td>
                            <td class="acciones-cell">
                                
                                <!-- FORMULARIO PARA REABRIR (DEVUELVE EL TICKET A SOPORTE.PHP) -->
                                <form action="actualizar_estado.php" method="POST" class="form-estado-inline">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <input type="hidden" name="origen" value="completados">
                                    <select name="nuevo_estado" onchange="this.form.submit()" class="select-estado select-reabrir">
                                        <option value="" disabled selected>Cambiar estado...</option>
                                        <option value="Abierto">🔄 Reabrir (A Principal)</option>
                                        <option value="En Proceso">⏳ En Proceso</option>
                                    </select>
                                </form>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">No hay tickets guardados en el historial de completados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>