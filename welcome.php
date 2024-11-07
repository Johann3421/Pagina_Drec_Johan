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

// Parámetro de filtro por fecha (si existe)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Parámetro de búsqueda (si existe)
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Consulta base para registros sin hora de salida
$sql = "SELECT * FROM visitas WHERE (hora_salida IS NULL OR hora_salida = '')";

// Si hay un parámetro de fecha, agregamos el filtro por fecha
if (!empty($fecha)) {
  $sql .= " AND fecha = ?";
}

// Si hay búsqueda, agregarla a la consulta
if (!empty($busqueda)) {
  $sql .= " AND (nombre LIKE ? OR dni LIKE ? OR smotivo LIKE ? OR lugar LIKE ?)";
}

// Agregamos la paginación
$sql .= " LIMIT ? OFFSET ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Si hay fecha y búsqueda, los incluimos en los parámetros
if (!empty($fecha) && !empty($busqueda)) {
  $busqueda_param = '%' . $busqueda . '%';
  $stmt->bind_param('sssssii', $fecha, $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $limite, $offset);
} elseif (!empty($fecha)) {
  $stmt->bind_param('sii', $fecha, $limite, $offset);
} elseif (!empty($busqueda)) {
  $busqueda_param = '%' . $busqueda . '%';
  $stmt->bind_param('ssssii', $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param, $limite, $offset);
} else {
  $stmt->bind_param('ii', $limite, $offset);
}

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

// Obtener el número total de registros que tienen hora_salida vacía (para paginación)
$sql_total = "SELECT COUNT(*) as total FROM visitas WHERE (hora_salida IS NULL OR hora_salida = '')";
if (!empty($fecha)) {
  $sql_total .= " AND fecha = ?";
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($fecha)) {
  $stmt_total->bind_param('s', $fecha);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_filas = $result_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_filas / $limite);
?>
<?php
// welcome.php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: index.php');
  exit();
}
?>



<!DOCTYPE html>
<html lang="es" style="height: auto;">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="AwHz0tWjMBpWFfS0XyLgAQhEw3dNiztPFnaACgCt">
  <title>Portalweb | Registro Visitas</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <!-- Google Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="script.js"></script>
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
            <li class="nav-item">
              <a href="Cronometro_Trabajadores/reporte_recesos.php" class="nav-link">
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
      <header class="header">
        <div class="logo">
          <img src="./imagenes/logo_dre.png" alt="Logo de la marca">
          <span class="logo-text">DIRECCION REGIONAL DE EDUCACION HUÁNUCO</span>
        </div>
        <nav>
          <ul class="nav-links">
            <li><a href="welcome.php">Visita</a></li>
            <li><a href="reporte.php">Reporte</a></li>
            <li><a href="./Cronometro_Trabajadores/Cronometro_welcome.php">Cronometro</a></li>
            <li><a href="./Cronometro_Trabajadores/reporte_recesos.php">Recesos</a></li>
          </ul>
        </nav>
      </header>

      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Registro visitas</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Main</a></li>
                <li class="breadcrumb-item active">Registro visitas</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- Main content -->
      <section class="content">
        <div id="myModal" class="modal fade" role="dialog"></div>
        <div class="row">
          <!-- cargador empresa -->
          <div style="display: none;" id="cargador1" align="center">
            <img src="././imagenes/Cargando.gif" align="middle" alt="cargador">
            &nbsp;<label style="color:#3C8DBC">Realizando tarea solicitada ...</label>
          </div>
          <!-- cargador 2 -->
          <div style="display: none;" id="cargador2" align="center">
            <img src="././imagenes/Loading_2.gif" align="middle" alt="cargador">
            &nbsp;<label style="color:#B9260E">Espere ...</label>
          </div>
        </div>

        <!-- Formulario de Registro de Visita -->
        <form id="frmvisita" class="frmvisita" method="post" action="procesar_visita.php" onsubmit="return validarFormulario();">
          <div class="container-fluid">
            <form id="frmvisita" class="frmvisita" method="post">
              <input type="hidden" name="_token" value="AwHz0tWjMBpWFfS0XyLgAQhEw3dNiztPFnaACgCt">
              <div class="row">
                <div class="col-sm-6">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title">Datos de visitante</h3>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12">
                          <label for="ndocu">DNI:</label>
                          <input type="text" maxlength="8" class="form-control form-control-sm" name="dni" id="ndocu" placeholder="Nro Documento" onkeypress="return esNumerico(event)" onkeydown="return noSubmitEnter(event)" onblur="buscarPorDNI()">
                          <div id="dni_error" class="text-danger" style="font-size: 12px;"></div>

                          <!-- Mostrar la imagen si está disponible -->
                          <?php if (!empty($foto_base64)): ?>
                            <img src="data:image/jpeg;base64,<?php echo $foto_base64; ?>" alt="Foto del usuario" style="max-width: 100px; margin-top: 10px;">
                          <?php endif; ?>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12">
                          <div class="form-group">
                            <label for="nombre">Nombres y Apellidos:</label>
                            <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" placeholder="Nombres y Apellidos">
                            <div id="nombre_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12">
                          <div class="form-group">
                            <label for="tipopersona">Tipo:</label>
                            <div class="tipopersona-options">
                              <input type="radio" id="personaNatural" name="tipopersona" value="Persona Natural">
                              <label for="personaNatural">Persona Natural</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                              <input type="radio" id="entidadPublica" name="tipopersona" value="Entidad Publica">
                              <label for="entidadPublica">Entidad Publica</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                              <input type="radio" id="entidadPrivada" name="tipopersona" value="Entidad Privada">
                              <label for="entidadPrivada">Entidad Privada</label>
                            </div>
                            <div id="tipopersona_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                          <div id="fginstitucion" class="form-group" style="display: none;">
                            <label id="linstitucion" for="institucion"></label>
                            <input type="text" id="institucion" class="form-control form-control-sm" name="institucion" onkeyup="convertToUppercase(this)" placeholder="Ejemp. Poder Judicial">
                            <div id="institucion_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Oficina a Visitar -->
                <div class="col-sm-6">
                  <div class="card card-secondary">
                    <div class="card-header">
                      <h3 class="card-title">Oficina a visitar</h3>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="form-group">
                            <label for="nomoficina">Oficina:</label>
                            <select id="nomoficina" class="form-control select2 form-control-sm" onchange="updateLugarByOficina()">
                              <option value="SELECCIONE" selected>&lt;&lt; SELECCIONE &gt;&gt;</option> <!-- Solo esta opción tiene selected -->
                              <!-- Más opciones aquí -->

                              <option value="ABASTECIMIENTO" data-id="558" data-select2-id="29">ABASTECIMIENTO</option>
                              <option value="ALMACEN" data-id="559" data-select2-id="30">ALMACEN</option>
                              <option value="ARCHIVO" data-id="554" data-select2-id="31">ARCHIVO</option>
                              <option value="AUDITORIO PRINCIPAL" data-id="602" data-select2-id="60">AUDITORIO PRINCIPAL</option>
                              <option value="AUDITORIO DESPACHO DIRECTORAL" data-id="603" data-select2-id="61">AUDITORIO DESPACHO DIRECTORAL</option>
                              <option value="BIENESTAR SOCIAL" data-id="564" data-select2-id="32">BIENESTAR SOCIAL</option>
                              <option value="CONTABILIDAD" data-id="563" data-select2-id="33">CONTABILIDAD</option>
                              <option value="CONSTANCIA DE PAGO" data-id="580" data-select2-id="57">CONSTANCIA DE PAGO</option>
                              <option value="DIRECCION DE ASESORIA JURIDICA" data-id="564" data-select2-id="56">DIRECCION DE ASESORIA JURIDICA</option>
                              <option value="DIRECCION DE GESTION ADMINISTRATIVA" data-id="170" data-select2-id="34">DIRECCION DE GESTION ADMINISTRATIVA</option>
                              <option value="DIRECCION DE GESTION INSTITUCIONAL" data-id="168" data-select2-id="35">DIRECCION DE GESTION INSTITUCIONAL</option>
                              <option value="DIRECCION DE GESTION PEDAGOGICA" data-id="167" data-select2-id="36">DIRECCION DE GESTION PEDAGOGICA</option>
                              <option value="DIRECCION REGIONAL DE EDUCACION-TRAMITE DOCUMENTARIO" data-id="197" data-select2-id="28">DIRECCION REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO</option>
                              <option value="DIRECCION REGIONAL" data-id="166" data-select2-id="37">DIRECCION REGIONAL</option>
                              <option value="ESCALAFON" data-id="556" data-select2-id="38">ESCALAFON</option>
                              <option value="ESTADISTICA" data-id="566" data-select2-id="39">ESTADISTICA</option>
                              <option value="INFORMATICA" data-id="567" data-select2-id="40">INFORMATICA</option>
                              <option value="INFRAESTRUCTURA" data-id="568" data-select2-id="41">INFRAESTRUCTURA</option>
                              <option value="OFICINA DE ASESORIA JURIDICA" data-id="169" data-select2-id="42">OFICINA DE ASESORIA JURIDICA</option>
                              <option value="OFICINA DE CONTROL INSTITUCIONAL" data-id="171" data-select2-id="43">OFICINA DE CONTROL INSTITUCIONAL</option>
                              <option value="PATRIMONIO" data-id="560" data-select2-id="44">PATRIMONIO</option>
                              <option value="PERSONAL" data-id="555" data-select2-id="45">PERSONAL</option>
                              <option value="PLANIFICACION" data-id="570" data-select2-id="46">PLANIFICACION</option>
                              <option value="PLANILLAS" data-id="557" data-select2-id="47">PLANILLAS</option>
                              <option value="PP 051 - PTCD" data-id="600" data-select2-id="58">PP 051 - PTCD</option>
                              <option value="PP 068 - PREVAED" data-id="601" data-select2-id="59">PP 068 - PREVAED</option>
                              <option value="PP  0147 - INSTITUTOS TECNOLOGICOS" data-id="604" data-select2-id="62">PP 0147 - INSTITUTOS TECNOLOGICOS</option>
                              <option value="PP 106 - CONVIVENCIA" data-id="605" data-select2-id="63">PP 106 - CONVIVENCIA</option>
                              <option value="PP 107 - ESPECIALISTA SEGUIMIENTO Y MONITOREO" data-id="606" data-select2-id="64">PP 107 - ESPECIALISTA SEGUIMIENTO Y MONITOREO</option>
                              <option value="PRESUPUESTO" data-id="571" data-select2-id="48">PRESUPUESTO</option>
                              <option value="PROYECTOS" data-id="572" data-select2-id="49">PROYECTOS</option>
                              <option value="RACIONALIZACION" data-id="565" data-select2-id="50">RACIONALIZACION</option>
                              <option value="RELACIONES PUBLICAS" data-id="1898" data-select2-id="51">RELACIONES PUBLICAS</option>
                              <option value="SECRETARIA GENERAL" data-id="553" data-select2-id="52">SECRETARIA GENERAL</option>
                              <option value="SECRETARIA TECNICA" data-id="2480" data-select2-id="53">SECRETARIA TECNICA</option>
                              <option value="SERVICIOS GENERALES" data-id="561" data-select2-id="2">SERVICIOS GENERALES</option>
                              <option value="TESORERÍA" data-id="562" data-select2-id="55">TESORERIA</option>
                            </select>
                            <div id="iddireccionesweb_error" class="text-danger" style="font-size: 12px;"></div>
                            <div id="oficodigo_error" class="text-danger" style="font-size: 12px;"></div>
                            <div id="nomoficina_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="form-group">
                            <label for="smotivo">Motivo de visita:</label>

                            <!-- Botones en lugar de select -->
                            <div id="motivo-buttons" class="btn-group" role="group">
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Reunion de trabajo')">Reunión de trabajo</button>
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Provision de servicios')">Provisión de servicios</button>
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Gestion de intereses')">Gestión de intereses</button>
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Motivo personal')">Motivo personal</button>
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Tramite documentario')">Trámite documentario</button>
                              <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Otros')">Otros</button>
                            </div>

                            <!-- Campo oculto para almacenar el valor seleccionado -->
                            <input type="hidden" id="smotivo" name="smotivo" value="">

                            <!-- Div para mostrar errores -->
                            <div id="motivo_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="form-group">
                            <label for="lugar">Lugar:</label>
                            <input type="text" class="form-control form-control-sm" id="lugar" name="lugar" placeholder="ID de la Oficina">
                            <div id="lugar_error" class="text-danger" style="font-size: 12px;"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer justify-content-between pt-2">
                      <button type="submit" class="btn btn-primary btn-xs float-right">
                        <i class="fa fa-upload"></i> Registrar visita
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </form>

        <!-- Lista de Visitas -->
        <div class="card card-info mb-3">
          <div class="card-header">
            <h3 class="card-title"><i class="fa fa-table"></i> LISTA DE VISITAS</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <form method="get" action="" class="mb-3">
                <div class="input-group">
                  <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="<?= htmlspecialchars($busqueda) ?>">
                  <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
              </form>
              <div class="col-lg-3 col-md-3 col-sm-12">

              </div>
            </div>
            <div class="row">
              <div class="table-responsive">
                <div id="tblvisita_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                  <div class="row col-lg-12 col-md-12 col-sm-12 arriba1">
                    <div class="dt-buttons btn-group flex-wrap">

                    </div>
                  </div>
                  <div class="arriba2">
                    <div class="dataTables_length" id="tblvisita_length">
                      <label>Show
                        <select name="tblvisita_length" aria-controls="tblvisita" class="custom-select custom-select-sm form-control form-control-sm">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="-1">Todo</option>
                        </select> entries
                      </label>
                    </div>
                    <div id="tblvisita_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                  </div>
                  <div class="abajo">
                    <table id="tblvisita" class="table display table-bordered table-striped dataTable no-footer" role="grid">
                      <thead class="thead-dark">
                        <tr role="row">
                          <th>Accion</th>
                          <th>Nro.</th>
                          <th>Fecha de visita</th>
                          <th>Visitante</th>
                          <th>Entidad del visitante</th>
                          <th>Documento del visitante</th>
                          <th>Hora Ingreso</th>
                          <th>Hora Salida</th>
                          <th>Motivo</th>
                          <th>Lugar Especifico</th>
                          <th>Imprimir Ticket</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                          $nro = $offset + 1;  // Ajustar numeración para paginación
                          while ($row = $result->fetch_assoc()) {
                            echo "<tr id='fila_{$row['id']}'>";
                            echo "<td><button class='btn btn-primary' onclick='registrarSalida({$row['id']})'><i class='material-icons'>exit_to_app</i></button></td>";
                            echo "<td>" . $nro++ . "</td>";
                            echo "<td>" . $row['fecha'] . "</td>";
                            echo "<td>" . $row['nombre'] . "</td>";
                            echo "<td>" . $row['tipopersona'] . "</td>";
                            echo "<td>" . $row['dni'] . "</td>";
                            echo "<td>" . (isset($row['hora_ingreso']) ? $row['hora_ingreso'] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['hora_salida']) ? $row['hora_salida'] : 'N/A') . "</td>";
                            echo "<td>" . $row['smotivo'] . "</td>";
                            echo "<td>" . $row['lugar'] . "</td>";
                            echo "<td><button class='btn btn-success' onclick='imprimirTicket({$row['id']})'><i class='material-icons'>print</i></button></td>";
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
          </div>
        </div>
      </section>
    </div>
    <!-- Footer -->
    <footer class="main-footer">
      <strong>&copy; 2024 <a href="#">Portalweb</a>.</strong> Todos los derechos reservados.
    </footer>
  </div>
  <!-- jQuery -->
  <script src="https://gestionportales.regionhuanuco.gob.pe/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="https://gestionportales.regionhuanuco.gob.pe/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE -->
  <script src="https://gestionportales.regionhuanuco.gob.pe/dist/js/adminlte.js"></script>
  <!-- Select2 -->
  <script src="https://gestionportales.regionhuanuco.gob.pe/plugins/select2/js/select2.full.min.js"></script>

</body>

</html>