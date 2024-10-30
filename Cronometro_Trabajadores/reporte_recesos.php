<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Parámetros de búsqueda y paginación
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$fechaDesde = isset($_GET['desde']) ? $_GET['desde'] : '';
$fechaHasta = isset($_GET['hasta']) ? $_GET['hasta'] : '';
$limite = 10;  // Registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

// Construir la consulta base
$sql_busqueda = "SELECT id, hora_receso, nombre, dni, duracion, exceso, hora_vuelta, estado 
                 FROM recesos 
                 WHERE (nombre LIKE ? OR dni LIKE ?)";


// Añadir filtro de fechas si están presentes
if (!empty($fechaDesde) && !empty($fechaHasta)) {
    $sql_busqueda .= " AND hora_receso BETWEEN ? AND ?";
}

// Añadir paginación
$sql_busqueda .= " LIMIT ? OFFSET ?";

// Preparar la consulta
$stmt = $conn->prepare($sql_busqueda);
$busqueda_param = '%' . $busqueda . '%';

// Vincular parámetros según el filtro de fechas
if (!empty($fechaDesde) && !empty($fechaHasta)) {
    $stmt->bind_param('sssii', $busqueda_param, $busqueda_param, $fechaDesde, $fechaHasta, $limite, $offset);
} else {
    $stmt->bind_param('ssii', $busqueda_param, $busqueda_param, $limite, $offset);
}

// Ejecutar la consulta y obtener resultados
$stmt->execute();
$result = $stmt->get_result();
$recesos = $result->fetch_all(MYSQLI_ASSOC);

// Calcular el total de registros sin paginación
$sql_total = "SELECT COUNT(*) as total FROM recesos WHERE (nombre LIKE ? OR dni LIKE ?)";
if (!empty($fechaDesde) && !empty($fechaHasta)) {
    $sql_total .= " AND hora_receso BETWEEN ? AND ?";
}
$stmt_total = $conn->prepare($sql_total);

// Vincular parámetros para el conteo total
if (!empty($fechaDesde) && !empty($fechaHasta)) {
    $stmt_total->bind_param('ssss', $busqueda_param, $busqueda_param, $fechaDesde, $fechaHasta);
} else {
    $stmt_total->bind_param('ss', $busqueda_param, $busqueda_param);
}

$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_filas = $result_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_filas / $limite);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Recesos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
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
                            <a href="Cronometro_welcome.php" class="nav-link">
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

        <!-- Content Wrapper -->
        <div class="content-wrapper" style="min-height: 678.031px;">
            <!-- Header Section -->
            <header class="header">
                <div class="logo-container">
                    <a href="/" class="logo">
                        <img src="../imagenes/logo_dre.png" alt="Logo de la marca">
                        <span class="logo-text">DIRECCION REGIONAL DE EDUCACION HUÁNUCO</span>
                    </a>
                </div>
                <nav>
                    <ul class="nav-links">
                        <li><a href="../welcome.php">Visita</a></li>
                        <li><a href="../reporte.php">Reporte</a></li>
                        <li><a href="Cronometro_welcome.php">Cronometro</a></li>
                        <li><a href="reporte_recesos.php">Recesos</a></li>
                    </ul>
                </nav>
            </header>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid my-4">
                    <h1>Reporte de Recesos</h1>

                    <!-- Botón para exportar a Excel -->
                    <div class="mb-3">
                        <a href="exportar_excel.php" class="btn btn-success">Exportar a Excel</a>
                    </div>

                    <!-- Formulario de búsqueda -->
                    <div class="card-body">
                        <!-- Cuadro de Búsqueda -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Búsqueda</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <label for="fecha-desde" class="form-label">Fecha Desde:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                        <input type="date" id="fecha-desde" class="form-control">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="fecha-hasta" class="form-label">Fecha Hasta:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                        <input type="date" id="fecha-hasta" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="ms-3 align-self-end">
                                                    <button type="button" class="btn btn-success" id="filtrar-fecha-btn">
                                                        <i class="fas fa-filter"></i> Filtrar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="w-50">
                                                <form method="get" action="">
                                                    <div class="input-group">
                                                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o DNI" value="<?= htmlspecialchars($busqueda) ?>">
                                                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Nro.</th>
                                                <th>Hora de Receso</th>
                                                <th>Trabajador</th>
                                                <th>DNI</th>
                                                <th>Duración Usada (min)</th>
                                                <th>Exceso (min)</th>
                                                <th>Hora de Vuelta</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recesos) > 0): ?>
                                                <?php foreach ($recesos as $index => $receso): ?>
                                                    <tr>
                                                        <td><?= $index + 1 + $offset ?></td>
                                                        <td><?= $receso['hora_receso'] ?></td>
                                                        <td><?= htmlspecialchars($receso['nombre']) ?></td>
                                                        <td><?= htmlspecialchars($receso['dni']) ?></td>
                                                        <td><?= htmlspecialchars($receso['duracion']) ?></td> <!-- Tiempo usado -->
                                                        <td><?= htmlspecialchars($receso['exceso'] ?? 0) ?></td> <!-- Muestra 0 si exceso no está definido -->
                                                        <td><?= $receso['hora_vuelta'] ?? 'Pendiente' ?></td>
                                                        <td><?= htmlspecialchars($receso['estado']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No hay recesos registrados.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>

                                </div>

                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                                                <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= $busqueda ?>&desde=<?= $fechaDesde ?>&hasta=<?= $fechaHasta ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <script>
            document.getElementById('filtrar-fecha-btn').addEventListener('click', function() {
                var fechaDesde = document.getElementById('fecha-desde').value;
                var fechaHasta = document.getElementById('fecha-hasta').value;

                if (fechaDesde && fechaHasta) {
                    window.location.href = "?desde=" + fechaDesde + "&hasta=" + fechaHasta;
                } else {
                    alert("Por favor, selecciona ambas fechas.");
                }
            });
        </script>
</body>

</html>
<?php
$conn->close();
?>