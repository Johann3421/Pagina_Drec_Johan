<?php
// Conexión a la base de datos
$dsn = "mysql:host=localhost;dbname=login_system;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, "root", "", $options);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Consulta para obtener trabajadores en receso activo con duración del receso
$stmt = $conn->prepare("SELECT id, nombre, dni, hora_receso, hora_vuelta, duracion 
                        FROM trabajadores 
                        WHERE hora_receso IS NOT NULL AND hora_vuelta IS NULL");
$stmt->execute();
$trabajadores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Receso de Trabajadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>

<body onload="iniciarContadores()" class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link">
                <div class="logo">
                    <img src="../imagenes/logo_dre.png" alt="Logo de la marca" class="brand-image img-circle elevation-3">
                    <span class="brand-text font-weight-light">DRE-HUÁNUCO</span>
                </div>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="../welcome.php" class="nav-link">
                                <i class="material-icons">person_add</i>
                                <p>Registrar visitas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../reporte.php" class="nav-link">
                                <i class="material-icons">assessment</i>
                                <p>Reporte</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="material-icons">access_time</i>
                                <p>Cronometro</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reporte_recesos.php" class="nav-link">
                                <i class="material-icons">assessment</i>
                                <p>Recesos</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <header class="header">
                <div class="logo">
                    <img src="../imagenes/logo_dre.png" alt="Logo de la marca">
                    <span class="logo-text">DIRECCION REGIONAL DE EDUCACION HUÁNUCO</span>
                </div>
                <nav>
                    <ul class="nav-links">
                        <li><a href="../welcome.php">Visita</a></li>
                        <li><a href="../reporte.php">Reporte</a></li>
                        <li><a href="#">Cronometro</a></li>
                        <li><a href="reporte_recesos.php">Recesos</a></li>
                    </ul>
                </nav>
            </header>

            <section class="content">
                <div class="row">
                    <div class="left-section">
                        <div class="search-worker mb-3">
                            <label for="searchWorker" class="form-label">Buscar Trabajador:</label>
                            <input type="text" id="searchWorker" class="form-control" placeholder="Ingrese el nombre del trabajador" onkeyup="buscarTrabajador()">
                            <div id="searchResult" class="mt-2"></div>
                        </div>

                        <input type="hidden" id="worker-id">
                        <input type="hidden" id="worker-name">
                        <input type="hidden" id="worker-dni">

                        <div id="main-worker" class="worker-box">
                            <h4 id="worker-name-display">Nombre del Trabajador</h4>
                            <div class="search-worker mb-3">
                                <label for="dniWorker" class="form-label">DNI:</label>
                                <input type="text" id="dniWorker" class="form-control" placeholder="Ingrese el DNI" maxlength="8" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="recesoDuration" class="form-label">Duración del Receso:</label>
                                <select id="recesoDuration" class="form-select">
                                    <option value="5">5 minutos</option>
                                    <option value="10">10 minutos</option>
                                    <option value="15">15 minutos</option>
                                    <option value="20">20 minutos</option>
                                    <option value="30">30 minutos</option>
                                </select>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="registrarReceso()">Generar Ticket</button>
                            </div>
                        </div>
                    </div>

                    <div class="right-section">
                        <div class="clock-container">
                            <div class="digital-clock">
                                <div class="time">
                                    <span class="hour">00</span> :
                                    <span class="minute">00</span> :
                                    <span class="second">00</span>
                                    <span class="ampm">AM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="abajo">
                    <table id="tblvisita" class="table display table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nro.</th>
                                <th>Trabajador</th>
                                <th>Documento</th>
                                <th>Hora de Receso</th>
                                <th>Hora de Vuelta</th>
                                <th>Duración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-visitas">
                            <?php if (!empty($trabajadores)) : ?>
                                <?php foreach ($trabajadores as $index => $trabajador) : ?>
                                    <tr id="fila_<?= $trabajador['id'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($trabajador['nombre']) ?></td>
                                        <td><?= htmlspecialchars($trabajador['dni']) ?></td>
                                        <td><?= $trabajador['hora_receso'] ?></td>
                                        <td><?= $trabajador['hora_vuelta'] ?? 'N/A' ?></td>
                                        <td>
                                            <span id="contador-<?= $trabajador['id'] ?>" class="contador contador-verde"></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" onclick="finalizarReceso(<?= $trabajador['id'] ?>)">Pausar Receso</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7">No hay datos disponibles</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>