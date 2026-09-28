<?php
session_start();
require 'conexion.php';

// Control de acceso
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: log.html");
    exit();
}

// Filtramos para mostrar SOLO los tickets NO completados en la vista principal
$query = "SELECT tickets.*, usuarios.nombre AS solicitante
          FROM tickets
          JOIN usuarios ON tickets.usuario_id = usuarios.id
          WHERE tickets.estado != 'Completado'
          ORDER BY tickets.fecha_creacion DESC";

$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Soporte TI - Incidencias Activas</title>
    <link rel="stylesheet" href="soportestyle.css">
</head>
<body>
    <header class="header-soporte">
        <div class="header-brand">
            <h1>Mesa de Ayuda - Soporte TI</h1>
            <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
        </div>
        <div class="header-actions">
            <a href="completados.php" class="btn-nav">Ver Tickets Completados 📁</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-soporte">
        <h2>Bandeja de Tickets Activos</h2>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Ubicación</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Solicitante</th>
                        <th>Acciones</th>
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
                            <td>
                                <span class="badge prioridad-<?php echo strtolower($ticket['prioridad']); ?>">
                                    <?php echo htmlspecialchars($ticket['prioridad']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge estado-<?php echo strtolower($ticket['estado']); ?>">
                                    <?php echo htmlspecialchars($ticket['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['solicitante']); ?></td>
                            <td class="acciones-cell">
                                
                                <!-- BOTÓN 1: MARCAR COMO COMPLETADO -->
                                <form action="actualizar_estado.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <input type="hidden" name="nuevo_estado" value="Completado">
                                    <button type="submit" class="btn-accion btn-completado">✔ Completar</button>
                                </form>

                                <!-- BOTÓN 2: CAMBIAR ESTADO (MANDAR O CAMBIAR DE FASE) -->
                                <form action="actualizar_estado.php" method="POST" class="form-estado-inline">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <select name="nuevo_estado" onchange="this.form.submit()" class="select-estado">
                                        <option value="" disabled selected>Editar Estado...</option>
                                        <option value="Abierto">Abierto</option>
                                        <option value="En Proceso">En Proceso</option>
                                        <option value="Resuelto">Resuelto</option>
                                    </select>
                                </form>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">No hay tickets activos en este momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>