<?php
// Configuración de la base de datos y paginación
$limite = 10;  // Número de registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

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

// Obtener el número total de registros
$sql_total = "SELECT COUNT(*) as total FROM visitas";
$result_total = $conn->query($sql_total);
$total_filas = $result_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_filas / $limite);

// Obtener los registros para la página actual
$sql = "SELECT * FROM visitas LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limite, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es" style="height: auto;">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="AwHz0tWjMBpWFfS0XyLgAQhEw3dNiztPFnaACgCt">
  <title>Portalweb | Registro Visitas</title>

  <link rel="icon" type="image/png" href="https://gestionportales.regionhuanuco.gob.pe/dist/img/favicon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/dist/css/adminlte.css">

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
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#" role="button">
            <i class="fas fa-user"></i> HOUSEN ELVIS
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/" class="brand-link">
        <img src="https://gestionportales.regionhuanuco.gob.pe/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Administración</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="http://goredigital.regionhuanuco.gob.pe/storage/avatar/logo.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block">HOUSEN ELVIS</a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!-- Principal -->
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="material-icons">home</i>
                <p>
                  Principal
                  <span class="right badge badge-danger">New</span>
                </p>
              </a>
            </li>

            <!-- Registro de visitas -->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="material-icons">event</i>
                <p>
                  Registro de visitas
                  <i class="material-icons right">expand_more</i>
                </p>
              </a>
              <ul class="nav nav-treeview" style="display: none;">
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
                  <a href="#" class="nav-link">
                    <i class="material-icons">visibility</i>
                    <p>Vista para exterior</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="Cronometro_Trabajadores/Cronometro_welcome.php" class="nav-link">
                    <i class="material-icons">access_time</i>
                    <p>Cronometro</p>
                  </a>
                </li>
              </ul>
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

                  <div class="abajo">
                    <table id="tblvisita" class="table display table-bordered table-striped dataTable no-footer" role="grid">
                      <thead class="thead-dark">
                        <tr role="row">
                          <th>Nro.</th>
                          <th>Fecha de visita</th>
                          <th>Visitante</th>
                          <th>Entidad del visitante</th>
                          <th>Documento del visitante</th>
                          <th>Hora Ingreso</th>
                          <th>Hora Salida</th>
                          <th>Motivo</th>
                          <th>Lugar Especifico</th>
                          <th>Observaciones</th>
                          <th>Imprimir Ticket</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                          $nro = $offset + 1;  // Ajustar numeración para paginación
                          while ($row = $result->fetch_assoc()) {
                            echo "<tr id='fila_{$row['id']}'>";
                            echo "<td>" . $nro++ . "</td>";
                            echo "<td>" . $row['fecha'] . "</td>";
                            echo "<td>" . $row['nombre'] . "</td>";
                            echo "<td>" . $row['tipopersona'] . "</td>";
                            echo "<td>" . $row['dni'] . "</td>";
                            echo "<td>" . (isset($row['hora_ingreso']) ? $row['hora_ingreso'] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['hora_salida']) ? $row['hora_salida'] : 'N/A') . "</td>";
                            echo "<td>" . $row['smotivo'] . "</td>";
                            echo "<td>" . $row['lugar'] . "</td>";
                            echo "<td>" . (isset($row['observaciones']) ? $row['observaciones'] : 'N/A') . "</td>";
                            echo "<td><button class='btn btn-success' onclick='imprimirTicket({$row['id']})'>Imprimir Ticket</button></td>";
                            echo "</tr>";
                          }
                        } else {
                          echo "<tr><td colspan='12'>No hay datos disponibles</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="dataTables_paginate paging_simple_numbers" id="tblvisita_paginate">
                    <ul class="pagination">
                      <?php if ($pagina > 1): ?>
                        <li class="paginate_button page-item previous">
                          <a href="?pagina=<?= $pagina - 1 ?>" class="page-link">Previous</a>
                        </li>
                      <?php else: ?>
                        <li class="paginate_button page-item previous disabled">
                          <a href="#" class="page-link">Previous</a>
                        </li>
                      <?php endif; ?>

                      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="paginate_button page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                          <a href="?pagina=<?= $i ?>" class="page-link"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>

                      <?php if ($pagina < $total_paginas): ?>
                        <li class="paginate_button page-item next">
                          <a href="?pagina=<?= $pagina + 1 ?>" class="page-link">Next</a>
                        </li>
                      <?php else: ?>
                        <li class="paginate_button page-item next disabled">
                          <a href="#" class="page-link">Next</a>
                        </li>
                      <?php endif; ?>
                    </ul>
                  </div>

                  <div class="clear"></div>
                </div>
              </div>
            </div>
                  </body>

</html>