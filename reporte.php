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

// Configuración de la paginación
$limite = 10; // Número de registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

// Consulta de búsqueda
$sql_busqueda = "SELECT * FROM visitas WHERE nombre LIKE ? OR dni LIKE ? OR smotivo LIKE ? OR lugar LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql_busqueda);
$busqueda_param = '%' . $busqueda . '%';
$stmt->bind_param('ssssii', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $limite, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Contar total de registros (sin paginación)
$sql_total = "SELECT COUNT(*) as total FROM visitas WHERE nombre LIKE ? OR dni LIKE ? OR smotivo LIKE ? OR lugar LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param('ssss', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param);
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
  <link rel="icon" type="image/png" href="https://gestionportales.regionhuanuco.gob.pe/dist/img/favicon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/dist/css/adminlte.css">
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
        <form method="get" action="" class="mb-3">
          <div class="input-group">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
          </div>
        </form>

        <!-- Tabla de visitas -->
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
            <?php
            if ($result->num_rows > 0) {
              $nro = $offset + 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $nro++ . "</td>";
                echo "<td>" . $row['fecha'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['dni'] . "</td>";
                echo "<td>" . $row['hora_ingreso'] . "</td>";
                echo "<td>" . $row['hora_salida'] . "</td>";
                echo "<td>" . $row['smotivo'] . "</td>";
                echo "<td>" . $row['lugar'] . "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='9'>No se encontraron resultados.</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <!-- Paginación -->
        <nav>
          <ul class="pagination">
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

    <script>
      function imprimirTicket(id) {
        window.location.href = "imprimir_ticket.php?id=" + id;
      }
    </script>
</body>

</html>
<?php
$conn->close();
?>