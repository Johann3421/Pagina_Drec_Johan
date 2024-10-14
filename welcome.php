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

// Obtener el número total de registros que tienen hora_salida vacía
$sql_total = "SELECT COUNT(*) as total FROM visitas WHERE hora_salida IS NULL OR hora_salida = ''";
$result_total = $conn->query($sql_total);
$total_filas = $result_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_filas / $limite);

// Obtener los registros para la página actual filtrando por hora_salida vacía
$sql = "SELECT * FROM visitas WHERE hora_salida IS NULL OR hora_salida = '' LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $limite, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>



<?php
// welcome.php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: index.php');
  exit();
}

$persona = null;
$dni_error = "";
$nombre = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $token = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';  // Token API
  $dni = $_POST['dni'] ?? '';
  $nombre = $_POST['nombre'] ?? '';

  // Validar si el DNI está vacío
  if (empty($dni)) {
    $dni_error = "El DNI es obligatorio.";
  } else {
    // Iniciar llamada a la API
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTPHEADER => array(
        'Referer: https://apis.net.pe/consulta-dni-api',
        'Authorization: Bearer ' . $token
      ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Obtener el código de respuesta HTTP
    curl_close($curl);

    // Decodificar la respuesta de la API
    $persona = json_decode($response);

    // Si hubo un error en la llamada a la API o la respuesta es incorrecta
    if ($httpCode !== 200 || isset($persona->error)) {
      $dni_error = "No se encontró el DNI o ocurrió un error en la consulta.";
    } else {
      // Construir el nombre completo
      $nombre = trim($persona->nombres . " " . $persona->apellidoPaterno . " " . $persona->apellidoMaterno);
    }
  }
}
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
                  <a href="#" class="nav-link">
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



    <!-- Content Wrapper -->
    <div class="content-wrapper" style="min-height: 678.031px;">
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
        <form id="frmvisita" class="frmvisita" method="post" action="procesar_visita.php">
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
                          <input type="text" maxlength="8" class="form-control form-control-sm" name="dni" id="ndocu" placeholder="Nro Documento" onkeypress="return esNumerico(event)" onblur="buscarPorDNI()">
                          <div id="dni_error" class="text-danger" style="font-size: 12px;"></div>
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
                            <select id="tipopersona" name="tipopersona" class="form-control form-control-sm">
                              <option value="0">&lt;&lt; SELECCIONE &gt;&gt;</option>
                              <option value="Persona Natural">Persona Natural</option>
                              <option value="Entidad Publica">Entidad Publica</option>
                              <option value="Entidad Privada">Entidad Privada</option>
                            </select>
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
                              <option value="BIENESTAR SOCIAL" data-id="564" data-select2-id="32">BIENESTAR SOCIAL</option>
                              <option value="CONTABILIDAD" data-id="563" data-select2-id="33">CONTABILIDAD</option>
                              <option value="CONSTANCIA DE PAGO" data-id="580" data-select2-id="57">CONSTANCIA DE PAGO</option>
                              <option value="DIRECCION DE ASESORIA JURIDICA"data-id="564" data-select2-id="56">DIRECCION DE ASESORIA JURIDICA</option>
                              <option value="DIRECCIÓN DE GESTIÓN ADMINISTRATIVA" data-id="170" data-select2-id="34">DIRECCIÓN DE GESTIÓN ADMINISTRATIVA</option>
                              <option value="DIRECCIÓN DE GESTIÓN INSTITUCIONAL" data-id="168" data-select2-id="35">DIRECCIÓN DE GESTIÓN INSTITUCIONAL</option>
                              <option value="DIRECCIÓN DE GESTIÓN PEDAGÓGICA" data-id="167" data-select2-id="36">DIRECCIÓN DE GESTIÓN PEDAGÓGICA</option>
                              <option value="DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO" data-id="197" data-select2-id="28">DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO</option>
                              <option value="DIRECCIÓN REGIONAL" data-id="166" data-select2-id="37">DIRECCIÓN REGIONAL</option>
                              <option value="ESCALAFON" data-id="556" data-select2-id="38">ESCALAFON</option>
                              <option value="ESTADÍSTICA" data-id="566" data-select2-id="39">ESTADÍSTICA</option>
                              <option value="INFORMÁTICA" data-id="567" data-select2-id="40">INFORMÁTICA</option>
                              <option value="INFRAESTRUCTURA" data-id="568" data-select2-id="41">INFRAESTRUCTURA</option>
                              <option value="OFICINA DE ASESORÍA JURÍDICA" data-id="169" data-select2-id="42">OFICINA DE ASESORÍA JURÍDICA</option>
                              <option value="OFICINA DE CONTROL INSTITUCIONAL" data-id="171" data-select2-id="43">OFICINA DE CONTROL INSTITUCIONAL</option>
                              <option value="PATRIMONIO" data-id="560" data-select2-id="44">PATRIMONIO</option>
                              <option value="PERSONAL" data-id="555" data-select2-id="45">PERSONAL</option>
                              <option value="PLANIFICACIÓN" data-id="570" data-select2-id="46">PLANIFICACIÓN</option>
                              <option value="PLANILLAS" data-id="557" data-select2-id="47">PLANILLAS</option>
                              <option value="PP 051 - PTCD" data-id="600" data-select2-id="58">PP 051 - PTCD</option>
                              <option value="PP 068 - PREVAED" data-id="601" data-select2-id="59">PP 068 - PREVAED</option>
                              <option value="PRESUPUESTO" data-id="571" data-select2-id="48">PRESUPUESTO</option>
                              <option value="PROYECTOS" data-id="572" data-select2-id="49">PROYECTOS</option>
                              <option value="RACIONALIZACIÓN" data-id="565" data-select2-id="50">RACIONALIZACIÓN</option>
                              <option value="RELACIONES PUBLICAS" data-id="1898" data-select2-id="51">RELACIONES PUBLICAS</option>
                              <option value="SECRETARIA GENERAL" data-id="553" data-select2-id="52">SECRETARIA GENERAL</option>
                              <option value="SECRETARIA TECNICA" data-id="2480" data-select2-id="53">SECRETARIA TECNICA</option>
                              <option value="SERVICIOS GENERALES" data-id="561" data-select2-id="2">SERVICIOS GENERALES</option>
                              <option value="TESORERÍA" data-id="562" data-select2-id="55">TESORERÍA</option>
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
              <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="form-group">
                  <input type="text" class="form-control form-control-sm" id="txtbusqueda" name="txtbusqueda" placeholder="Ingrese aqui el nombre, entidad o cargo de la persona...">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="form-group">
                  <input type="hidden" id="fechabusqueda" name="fechabusqueda" value="07/10/2024 - 07/10/2024">
                  <div class="input-group">
                    <button type="button" class="btn btn-default float-right" id="daterange-btn">
                      <span><i class="fa fa-calendar-alt"></i>&nbsp;&nbsp;&nbsp;07/10/2024 - 07/10/2024</span>
                      <i class="fas fa-caret-down"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="form-group">
                  <button type="button" class="btn btn-info btn-xs float-left" id="btnbuscar">
                    <i class="fa fa-search-plus"></i>
                    Buscar
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="table-responsive">
                <div id="tblvisita_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                  <div class="row col-lg-12 col-md-12 col-sm-12 arriba1">
                    <div class="dt-buttons btn-group flex-wrap">
                      <button class="btn btn-secondary buttons-excel buttons-html5 btn-success btn-sm float-right" tabindex="0" aria-controls="tblvisita" type="button">
                        <span><i class="fa fa-file-excel"></i>&nbsp;Excel</span>
                      </button>
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
                            echo "<td>" . (isset($row['observaciones']) ? $row['observaciones'] : 'N/A') . "</td>";
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
  <script>
    function buscarPorDNI() {
      var dni = document.getElementById("ndocu").value;

      if (dni.length === 8) { // Asegúrate de que el DNI tenga 8 caracteres
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "buscar_dni.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            if (response.success) {
              document.getElementById("nombre").value = response.nombre;
              document.getElementById("dni_error").innerHTML = "";
            } else {
              document.getElementById("dni_error").innerHTML = response.error;
              document.getElementById("nombre").value = ""; // Limpia el campo de nombre en caso de error
            }
          }
        };

        xhr.send("dni=" + dni);
      }
    }
  </script>
  <script>
    function abrirModal(id) {
      // Mostrar modal para registrar salida y observaciones
      var modalHtml = `
        <div class="modal" id="modalSalida">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Salida</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label for="observacion">Observación:</label>
                        <input type="text" id="observacion" class="form-control">
                        <input type="hidden" id="visitaId" value="${id}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="registrarSalida()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>`;
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      $('#modalSalida').modal('show');
    }

    // Abrir el modal para registrar salida y capturar el ID
    function abrirModalSalida(id) {
      document.getElementById("visitaIdModal").value = id; // Asignar el ID de la visita al campo oculto
      document.getElementById("observacionModal").value = ""; // Limpiar el campo de observación
      $('#modalSalida').modal('show'); // Mostrar el modal
    }

    // Registrar salida y ocultar la fila después de registrar
    // Registrar salida y ocultar la fila después de registrar
    function registrarSalida(id) {
      // Realizar una solicitud AJAX para actualizar los datos
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "registrar_salida.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          if (xhr.responseText.includes("Salida registrada correctamente.")) {
            alert("Salida registrada correctamente.");

            // Ocultar la fila después de registrar la salida
            document.getElementById("fila_" + id).style.display = "none";
          } else {
            alert("Error al registrar la salida: " + xhr.responseText);
          }
        }
      };

      xhr.send("id=" + id); // Solo enviamos el ID
    }



    function imprimirTicket(id) {
      // Crea un iframe invisible para cargar el PDF
      let iframe = document.createElement('iframe');
      iframe.style.display = 'none';
      iframe.src = "imprimir_ticket.php?id=" + id; // Ruta a tu archivo PHP que genera el PDF
      document.body.appendChild(iframe);

      // Espera a que el PDF se cargue en el iframe, luego imprime directamente
      iframe.onload = function() {
        iframe.contentWindow.print();
      };
    }

    function selectMotivo(button, value) {
      // Remover el estilo seleccionado de todos los botones
      const buttons = document.querySelectorAll('#motivo-buttons .btn');
      buttons.forEach(btn => btn.classList.remove('btn-primary'));
      buttons.forEach(btn => btn.classList.add('btn-outline-primary'));

      // Aplicar estilo seleccionado al botón clicado
      button.classList.remove('btn-outline-primary');
      button.classList.add('btn-primary');

      // Almacenar el valor en el campo oculto
      document.getElementById('smotivo').value = value;
    }

    function updateLugarByOficina() {
      // Obtener el elemento select
      var select = document.getElementById('nomoficina');

      // Obtener el texto de la opción seleccionada (el nombre de la oficina)
      var selectedText = select.options[select.selectedIndex].text;

      // Autocompletar el campo lugar con el texto de la oficina seleccionada
      document.getElementById('lugar').value = selectedText;
    }
  </script>


</body>

</html>