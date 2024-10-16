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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <!-- Google Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">




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



    <!-- Content Wrapper -->
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
                            <div class="form-group">
                              <label><strong>Seleccione la Oficina:</strong></label>
                              <div class="row">
                                <!-- Primera columna -->
                                <div class="col-md-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina0" value="SELECCIONE" checked>
                                    <label class="form-check-label" for="oficina0">&lt;&lt; SELECCIONE &gt;&gt;</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina1" value="ABASTECIMIENTO" data-id="558">
                                    <label class="form-check-label" for="oficina1">ABASTECIMIENTO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina2" value="ALMACEN" data-id="559">
                                    <label class="form-check-label" for="oficina2">ALMACEN</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina3" value="ARCHIVO" data-id="554">
                                    <label class="form-check-label" for="oficina3">ARCHIVO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina4" value="AUDITORIO PRINCIPAL" data-id="602">
                                    <label class="form-check-label" for="oficina4">AUDITORIO PRINCIPAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina5" value="AUDITORIO DESPACHO DIRECTORAL" data-id="603">
                                    <label class="form-check-label" for="oficina5">AUDITORIO DESPACHO DIRECTORAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina6" value="BIENESTAR SOCIAL" data-id="564">
                                    <label class="form-check-label" for="oficina6">BIENESTAR SOCIAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina7" value="CONTABILIDAD" data-id="563">
                                    <label class="form-check-label" for="oficina7">CONTABILIDAD</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina8" value="CONSTANCIA DE PAGO" data-id="580">
                                    <label class="form-check-label" for="oficina8">CONSTANCIA DE PAGO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina9" value="DIRECCION DE ASESORIA JURIDICA" data-id="564">
                                    <label class="form-check-label" for="oficina9">DIRECCION DE ASESORIA JURIDICA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina10" value="DIRECCION DE GESTION ADMINISTRATIVA" data-id="170">
                                    <label class="form-check-label" for="oficina10">DIRECCION DE GESTION ADMINISTRATIVA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina11" value="DIRECCION DE GESTION INSTITUCIONAL" data-id="168">
                                    <label class="form-check-label" for="oficina11">DIRECCION DE GESTION INSTITUCIONAL</label>
                                  </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina11" value="ESCALAFON" data-id="168">
                                    <label class="form-check-label" for="oficina12">ESCALAFON</label>
                                  </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina11" value="ESTADISTICA" data-id="168">
                                    <label class="form-check-label" for="oficina13">ESTADISTICA</label>
                                  </div>
                                </div>


                                <!-- Segunda columna -->
                                <div class="col-md-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina12" value="INFORMATICA" data-id="567">
                                    <label class="form-check-label" for="oficina14">INFORMATICA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina13" value="INFRAESTRUCTURA" data-id="568">
                                    <label class="form-check-label" for="oficina15">INFRAESTRUCTURA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina14" value="OFICINA DE CONTROL INSTITUCIONAL" data-id="171">
                                    <label class="form-check-label" for="oficina16">OFICINA DE CONTROL INSTITUCIONAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina15" value="PATRIMONIO" data-id="560">
                                    <label class="form-check-label" for="oficina17">PATRIMONIO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina16" value="PERSONAL" data-id="555">
                                    <label class="form-check-label" for="oficina18">PERSONAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina17" value="PLANIFICACION" data-id="570">
                                    <label class="form-check-label" for="oficina19">PLANIFICACION</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina18" value="PLANILLAS" data-id="557">
                                    <label class="form-check-label" for="oficina20">PLANILLAS</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina19" value="PP 051 - PTCD" data-id="600">
                                    <label class="form-check-label" for="oficina21">PP 051 - PTCD</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina20" value="PP 068 - PREVAED" data-id="601">
                                    <label class="form-check-label" for="oficina22">PP 068 - PREVAED</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina20" value="PP 0147 - INSTITUTOS TECNOLOGICOS" data-id="601">
                                    <label class="form-check-label" for="oficina23">PP 0147 - INSTITUTOS TECNOLOGICOS</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina21" value="PP 106 - CONVIVENCIA" data-id="605">
                                    <label class="form-check-label" for="oficina24">PP 106 - CONVIVENCIA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina22" value="PP 107 - ESPECIALISTA SEGUIMIENTO Y MONITOREO" data-id="606">
                                    <label class="form-check-label" for="oficina25">PP 107 - ESPECIALISTA SEGUIMIENTO Y MONITOREO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina22" value="PRESUPUESTO" data-id="606">
                                    <label class="form-check-label" for="oficina26">PRESUPUESTO</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina22" value="PROYECTOS" data-id="606">
                                    <label class="form-check-label" for="oficina27">PROYECTOS</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina22" value="RACIONALIZACION" data-id="606">
                                    <label class="form-check-label" for="oficina28">RACIONALIZACION</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina22" value="RELACIONES PUBLICAS" data-id="606">
                                    <label class="form-check-label" for="oficina29">RELACIONES PUBLICAS</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina23" value="SECRETARIA GENERAL" data-id="553">
                                    <label class="form-check-label" for="oficina30">SECRETARIA GENERAL</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina23" value="SECRETARIA TECNICA" data-id="553">
                                    <label class="form-check-label" for="oficina31">SECRETARIA TECNICA</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina24" value="SERVICIOS GENERALES" data-id="561">
                                    <label class="form-check-label" for="oficina32">SERVICIOS GENERALES</label>
                                  </div>

                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomoficina" id="oficina24" value="TESORERIA" data-id="561">
                                    <label class="form-check-label" for="oficina33">TESORERIA</label>
                                  </div>
                                </div>
                              </div>
                            </div>
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
                <div class="form-group">
                  <input type="hidden" id="fechabusqueda" name="fechabusqueda" value="07/10/2024 - 07/10/2024">
                  <div class="input-group mb-3">
                    <input type="date" id="fecha-filtro" class="form-control" placeholder="Selecciona una fecha">
                    <span class="input-group-append">
                      <button type="button" class="btn btn-default" id="filtrar-fecha-btn">
                        <i class="fa fa-calendar-alt"></i> Filtrar
                      </button>
                    </span>
                  </div>


                </div>
              </div>
            </div>
            <div class="row">
              <div class="table-responsive">
                <div id="tblvisita_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                  <div class="row col-lg-12 col-md-12 col-sm-12 arriba1">
                    <div class="dt-buttons btn-group flex-wrap">
                      <div class="mb-3">
                        <a href="exportar_excel.php" class="btn btn-success">Exportar a Excel</a>
                      </div>
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
    function registrarSalida(id) {
      // Realizar una solicitud AJAX para actualizar los datos
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "registrar_salida.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          if (xhr.responseText.includes("Salida registrada correctamente.")) {
            // Mostrar el mensaje temporalmente sin un alert
            mostrarMensajeTemporal("Salida registrada correctamente.", "success");

            // Ocultar la fila después de registrar la salida
            document.getElementById("fila_" + id).style.display = "none";
          } else {
            mostrarMensajeTemporal("Error al registrar la salida: " + xhr.responseText, "error");
          }
        }
      };

      xhr.send("id=" + id); // Solo enviamos el ID
    }

    function mostrarMensajeTemporal(mensaje, tipo) {
      // Crear un div temporal para el mensaje
      var mensajeDiv = document.createElement("div");
      mensajeDiv.textContent = mensaje;
      mensajeDiv.style.position = "fixed";
      mensajeDiv.style.top = "10px";
      mensajeDiv.style.right = "10px";
      mensajeDiv.style.padding = "10px";
      mensajeDiv.style.backgroundColor = tipo === "success" ? "#28a745" : "#dc3545";
      mensajeDiv.style.color = "white";
      mensajeDiv.style.borderRadius = "5px";
      mensajeDiv.style.boxShadow = "0px 0px 10px rgba(0, 0, 0, 0.1)";

      // Agregar el mensaje al cuerpo del documento
      document.body.appendChild(mensajeDiv);

      // Eliminar el mensaje después de 3 segundos
      setTimeout(function() {
        mensajeDiv.remove();
      }, 3000);
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

    document.getElementById('filtrar-fecha-btn').addEventListener('click', function() {
      var fechaSeleccionada = document.getElementById('fecha-filtro').value;

      if (fechaSeleccionada) {
        // Redirigir a la misma página con el parámetro de fecha
        window.location.href = "?fecha=" + fechaSeleccionada;
      } else {
        alert("Por favor, selecciona una fecha.");
      }
    });
  </script>


</body>

</html>