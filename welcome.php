<?php
// welcome.php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$persona = null;
$dni_error = "";
$nombre = "";
$foto = ""; // Variable para almacenar la URL de la foto

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';  // Token API
    $dni = $_POST['dni'];

    // Validar si el DNI está vacío
    if (empty($dni)) {
        $dni_error = "El DNI es obligatorio.";
    } else {
        // Iniciar llamada a la API
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: https://apis.net.pe/consulta-dni-api',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Decodificar la respuesta de la API
        $persona = json_decode($response);

        // Si la API devuelve un error
        if (isset($persona->error)) {
            $dni_error = "No se encontró el DNI.";
        } else {
            // Obtener nombre completo
            $nombre = $persona->nombres . " " . $persona->apellidoPaterno . " " . $persona->apellidoMaterno;

            // Verificar si hay una URL de foto en la respuesta
            if (isset($persona->foto)) {
                $foto = $persona->foto; // Guardar la URL de la foto
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" style="height: auto;">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= isset($_SESSION['_token']) ? $_SESSION['_token'] : '' ?>">
  <title>Portalweb | Registro Visitas</title>
  
  <link rel="icon" type="image/png" href="https://gestionportales.regionhuanuco.gob.pe/dist/img/favicon.png">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/dist/css/adminlte.css">

  <style>
    .arriba1 { float: right; margin-left: 0; margin-bottom: 5px; display: block; margin-right: 5px; }
    .btn-group { display: block !important; }
    .head-modal { background-color: #1367C8; color: white; }
    .foto { width: 100px; height: 100px; border-radius: 50%; } /* Estilo para la imagen */
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
    <a href="/" class="brand-link">
      <img src="https://gestionportales.regionhuanuco.gob.pe/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Administración</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="http://goredigital.regionhuanuco.gob.pe/storage/avatar/logo.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">HOUSEN ELVIS</a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="https://gestionportales.regionhuanuco.gob.pe" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Principal
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>

          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Registro de visitas
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: block;">
              <li class="nav-item">
                <a href="https://gestionportales.regionhuanuco.gob.pe/regvisitas" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Registrar visitas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://gestionportales.regionhuanuco.gob.pe/reportevisit" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reporte</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://gestionportales.regionhuanuco.gob.pe/reporte_extrenovisitas" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vista para exterior</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper" style="min-height: 678.031px;">
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

    <section class="content">
      <div id="myModal" class="modal fade" role="dialog"></div>
      <div class="row">
        <div style="display: none;" id="cargador1" align="center">
          <img src="https://gestionportales.regionhuanuco.gob.pe/app-assets/images/cargando.gif" align="middle" alt="cargador">
          &nbsp;<label style="color:#3C8DBC">Realizando tarea solicitada ...</label>
        </div>
        <div style="display: none;" id="cargador2" align="center">
          <img src="https://gestionportales.regionhuanuco.gob.pe/app-assets/images/load.gif" align="middle" alt="cargador">
          &nbsp;<label style="color:#B9260E">Espere ...</label>
        </div>
      </div>

      <div class="container-fluid">
        <form id="frmvisita" class="frmvisita" method="post">
          <!-- Eliminar input de token si no estás manejando CSRF -->
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
                      <input type="text" maxlength="8" class="form-control form-control-sm" name="dni" id="ndocu" placeholder="Nro Documento" onkeypress="return esNumerico(event)" autofocus="">
                      <div id="dni_error" class="text-danger" style="font-size: 12px;"><?= $dni_error ?></div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12">
                      <div class="form-group">
                        <label for="nombre">Nombres y Apellidos:</label>
                        <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" value="<?= $nombre ?>" placeholder="Nombres y Apellidos">
                        <div id="nombre_error" class="text-danger" style="font-size: 12px;"></div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                      <!-- Aquí se cargará la imagen -->
                      <div id="foto">
                        <?php if ($foto): ?>
                          <img src="<?= $foto ?>" class="foto" alt="Foto de Visitante">
                        <?php endif; ?>
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
                        <input type="hidden" id="oficodigo" name="oficodigo" value="561">
                        <input type="hidden" id="iddireccionesweb" name="iddireccionesweb" value="3">
                        <input type="hidden" id="nomoficinai" name="nomoficina" value="DRE/SERVICIOS GENERALES">
                        <select id="nomoficina" class="form-control select2 form-control-sm">
                          <option selected="" value="0">&lt;&lt; SELECCIONE &gt;&gt;</option>
                          <option value="DRE/SERVICIOS GENERALES" selected="">DRE/SERVICIOS GENERALES</option>
                          <!-- Más opciones aquí -->
                        </select>
                        <div id="iddireccionesweb_error" class="text-danger" style="font-size: 12px;"></div>
                        <div id="oficodigo_error" class="text-danger" style="font-size: 12px;"></div>
                        <div id="nomoficina_error" class="text-danger" style="font-size: 12px;"></div>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      
                      <div class="form-group">
                        <label for="smotivo">Motivo de visita :</label>
                        <select id="smotivo" name="smotivo" class="form-control form-control-sm">
                          <option value="0">&lt;&lt; SELECCIONE &gt;&gt;</option>
                          <option value="Reunion de trabajo">Reunión de trabajo</option>
                          <!-- Más opciones aquí -->
                        </select>
                        <div id="motivo_error" class="text-danger" style="font-size: 12px;"></div>
                      </div>
                      <div id="fgmotivo" class="form-group" style="display: none;">
                        <label for="motivo">Especifique motivo :</label>
                        <input type="text" id="motivo" class="form-control form-control-sm" name="motivo" onkeyup="convertToUppercase(this)" placeholder="Ejemp. Asunto Personal">
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="form-group">
                        <label for="lugar">Lugar :</label>
                        <input type="text" class="form-control form-control-sm" id="lugar" name="lugar" onkeyup="convertToUppercase(this)" placeholder="Ejemp. Edificio A - Piso 2">
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
  $(document).ready(function() {
    $('.select2').select2();
  });
</script>

</body>
</html>
