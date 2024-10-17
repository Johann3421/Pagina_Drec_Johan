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

// Parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$fechaDesde = isset($_GET['desde']) ? $_GET['desde'] : ''; // Fecha inicial
$fechaHasta = isset($_GET['hasta']) ? $_GET['hasta'] : ''; // Fecha final

// Configuración de la paginación
$limite = 10; // Número de registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

// Consulta de búsqueda con filtro por fecha si existe
$sql_busqueda = "SELECT * FROM visitas WHERE (nombre LIKE ? OR dni LIKE ? OR smotivo LIKE ? OR lugar LIKE ?)";

// Si se ha seleccionado un rango de fechas, agregarlo a la consulta
if (!empty($fechaDesde) && !empty($fechaHasta)) {
  $sql_busqueda .= " AND fecha BETWEEN ? AND ?";
}

// Agregar paginación
$sql_busqueda .= " LIMIT ? OFFSET ?";

// Preparar la consulta
$stmt = $conn->prepare($sql_busqueda);

// Parámetros de búsqueda
$busqueda_param = '%' . $busqueda . '%';

// Vincular parámetros según si hay o no un rango de fechas
if (!empty($fechaDesde) && !empty($fechaHasta)) {
  $stmt->bind_param('ssssssii', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $fechaDesde, $fechaHasta, $limite, $offset);
} else {
  $stmt->bind_param('ssssii', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $limite, $offset);
}

// Ejecutar consulta
$stmt->execute();
$result = $stmt->get_result();

// Contar total de registros (sin paginación)
$sql_total = "SELECT COUNT(*) as total FROM visitas WHERE (nombre LIKE ? OR dni LIKE ? OR smotivo LIKE ? OR lugar LIKE ?)";

// Agregar filtro de fecha si está presente
if (!empty($fechaDesde) && !empty($fechaHasta)) {
  $sql_total .= " AND fecha BETWEEN ? AND ?";
}

// Preparar y ejecutar la consulta de conteo total
$stmt_total = $conn->prepare($sql_total);

if (!empty($fechaDesde) && !empty($fechaHasta)) {
  $stmt_total->bind_param('ssssss', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $fechaDesde, $fechaHasta);
} else {
  $stmt_total->bind_param('ssss', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param);
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
  <title>Reporte</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="styles.css">

  <style>
    .arriba1 {
      float: right;
      margin-left: 0;
      margin-bottom: 5px;
      display: block;
      margin-right: 5px;
    }

    .btn-group {
      display: block !important;
    }

    .head-modal {
      background-color: #1367C8;
      color: white;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/" class="brand-link">
        <div class="logo">
          <img src="./imagenes/logo_dre.png" alt="Logo de la marca" class="brand-image img-circle elevation-3">
          <span class="brand-text font-weight-light">DRE-HUÁNUCO</span>
        </div>
      </a>


      <!-- Sidebar -->
      <div class="sidebar">
        <!-- User Panel -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Principal -->

            <!-- Registro de visitas -->
            <li class="nav-item">
              <a href="welcome.php" class="nav-link">
                <i class="material-icons">person_add</i>
                <p>Registrar visitas</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="reporte.php" class="nav-link">
                <i class="material-icons">assessment</i>
                <p>Reporte</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Cronometro_Trabajadores/Cronometro_welcome.php" class="nav-link">
                <i class="material-icons">access_time</i>
                <p>Cronometro</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Simple JS para desplegar menú -->
    <script>
      document.querySelectorAll('.nav-item.has-treeview > a').forEach(function(menu) {
        menu.addEventListener('click', function(e) {
          e.preventDefault();
          const submenu = this.nextElementSibling;
          submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
        });
      });
    </script>

    <div class="content-wrapper" style="min-height: 678.031px;">
      <header class="header">
        <div class="logo">
          <img src="./imagenes/logo_dre.png" alt="Logo de la marca">
          <span class="logo-text">DIRECCION REGIONAL DE EDUCACION HUÁNUCO</span>
        </div>
        <nav>
          <ul class="nav-links">
            <li><a href="welcome.php">Registrar Visita</a></li>
            <li><a href="reporte.php">Reporte</a></li>
            <li><a href="./Cronometro_Trabajadores/Cronometro_welcome.php">Cronometro</a></li>
          </ul>
        </nav>
      </header>
      <div class="container-fluid my-4">
        <h1>Reporte de Salidas</h1>

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
              <!-- Row para el buscador y el filtro por fecha en el mismo div de manera horizontal -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="d-flex justify-content-between align-items-center">
                    <!-- Filtro por fecha a la izquierda -->
                    <div class="d-flex">
                      <div class="me-3">
                        <!-- Campo de Fecha Desde -->
                        <label for="fecha-desde" class="form-label">Fecha Desde:</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                          <input type="date" id="fecha-desde" class="form-control">
                        </div>
                      </div>

                      <div>
                        <!-- Campo de Fecha Hasta -->
                        <label for="fecha-hasta" class="form-label">Fecha Hasta:</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                          <input type="date" id="fecha-hasta" class="form-control">
                        </div>
                      </div>

                      <!-- Botón de Filtrar a la derecha del filtro por fecha -->
                      <div class="ms-3 align-self-end">
                        <button type="button" class="btn btn-success" id="filtrar-fecha-btn">
                          <i class="fas fa-filter"></i> Filtrar
                        </button>
                      </div>
                    </div>

                    <!-- Buscador a la derecha -->
                    <div class="w-50">
                      <form method="get" action="">
                        <div class="input-group">
                          <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="<?= htmlspecialchars($busqueda) ?>">
                          <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabla de visitas dentro del cuadro de búsqueda -->
              <div class="table-responsive mb-3">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark">
                    <tr>
                      <th>Nro.</th>
                      <th>Fecha de visita</th>
                      <th>Visitante</th>
                      <th>Documento del visitante</th>
                      <th>Hora Ingreso</th>
                      <th>Hora Salida</th>
                      <th>Motivo</th>
                      <th>Lugar Específico</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($result->num_rows > 0): ?>
                      <?php $nro = $offset + 1; ?>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= $nro++ ?></td>
                          <td><?= $row['fecha'] ?></td>
                          <td><?= $row['nombre'] ?></td>
                          <td><?= $row['dni'] ?></td>
                          <td><?= $row['hora_ingreso'] ?></td>
                          <td><?= $row['hora_salida'] ?></td>
                          <td><?= $row['smotivo'] ?></td>
                          <td><?= $row['lugar'] ?></td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">No se encontraron resultados.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Paginación dentro del cuadro de búsqueda -->
              <nav>
                <ul class="pagination justify-content-center">
                  <?php if ($pagina > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&busqueda=<?= htmlspecialchars($busqueda) ?>">Anterior</a>
                    </li>
                  <?php endif; ?>
                  <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                      <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= htmlspecialchars($busqueda) ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                  <?php if ($pagina < $total_paginas): ?>
                    <li class="page-item">
                      <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&busqueda=<?= htmlspecialchars($busqueda) ?>">Siguiente</a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>

        <!-- JavaScript para manejar el filtrado por fecha -->
        <script>
          document.getElementById('filtrar-fecha-btn').addEventListener('click', function() {
            var fechaDesde = document.getElementById('fecha-desde').value;
            var fechaHasta = document.getElementById('fecha-hasta').value;

            if (fechaDesde && fechaHasta) {
              // Redirigir con parámetros de fecha
              window.location.href = "?desde=" + fechaDesde + "&hasta=" + fechaHasta;
            } else {
              alert("Por favor, selecciona ambas fechas.");
            }
          });
        </script>


        <!-- JavaScript para manejar el filtrado por fecha -->
        <script>
          document.getElementById('filtrar-fecha-btn').addEventListener('click', function() {
            var fechaDesde = document.getElementById('fecha-desde').value;
            var fechaHasta = document.getElementById('fecha-hasta').value;

            if (fechaDesde && fechaHasta) {
              // Redirigir con parámetros de fecha
              window.location.href = "?desde=" + fechaDesde + "&hasta=" + fechaHasta;
            } else {
              alert("Por favor, selecciona ambas fechas.");
            }
          });
        </script>



        <script>
          function imprimirTicket(id) {
            window.location.href = "imprimir_ticket.php?id=" + id;
          }
          document.getElementById('filtrar-fecha-btn').addEventListener('click', function() {
            var fechaDesde = document.getElementById('fecha-desde').value;
            var fechaHasta = document.getElementById('fecha-hasta').value;

            if (fechaDesde && fechaHasta) {
              // Redirigir con parámetros de fecha
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