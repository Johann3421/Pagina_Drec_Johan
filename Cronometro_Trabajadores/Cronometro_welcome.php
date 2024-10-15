<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Receso de Trabajadores</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fuente Google: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- FontAwesome (iconos) -->
    <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE Styles -->
    <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/dist/css/adminlte.css">

    <!-- Custom CSS -->
    <style>
        .timer {
            font-size: 24px;
            font-weight: bold;
        }

        .over-time {
            color: red;
        }

        .in-time {
            color: green;
        }

        .worker-box {
            border: 1px solid #ccc;
            padding: 20px;
            /* Más espacio dentro del cuadro */
            margin: 20px auto;
            /* Centrar y mayor espacio vertical */
            max-width: 600px;
            /* Limitar el ancho máximo */
            border-radius: 10px;
            background-color: #f9f9f9;
            /* Fondo claro para destacar el cuadro */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            /* Añadir sombra para un efecto flotante */
        }


        .small-box {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 250px;
            z-index: 9999;
            background-color: #f8f9fa;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        #clock {
            font-size: 36px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            /* Color más oscuro para mejor visibilidad */
        }

        .mb-4 {
            margin-bottom: 40px !important;
            /* Más espacio entre los cuadros */
            text-align: center;
            /* Centramos el cuadro de búsqueda */
        }

        #searchWorker {
            max-width: 400px;
            /* Limitar el ancho del cuadro de búsqueda */
            margin: 0 auto;
            /* Centrar el cuadro */
        }

        #searchResult div {
            cursor: pointer;
            padding: 10px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #ccc;
        }

        #searchResult div:hover {
            background-color: #ddd;
        }


        @media (max-width: 768px) {
            .worker-box {
                text-align: center;
            }
        }

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

        .wrapper {
            padding: 30px 15px;
            /* Añadir un padding interno general */
        }

        h1.mb-4 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: bold;
            color: #1367C8;
            /* Color más destacado para el título */
        }

        button.btn {
            margin: 5px;
            width: 150px;
            /* Igualar el tamaño de los botones */
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

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <img src="https://gestionportales.regionhuanuco.gob.pe/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Administración</span>
            </a>

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
                                <p>Principal</p>
                            </a>
                        </li>

                        <!-- Registro de visitas -->
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
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <h1 class="mb-4">Control de Receso de Trabajadores</h1>

        <!-- Reloj en tiempo real -->
        <div id="clock" class="mb-5"></div>

        <!-- Cuadro de búsqueda -->
        <div class="mb-4">
            <label for="searchWorker" class="form-label">Buscar Trabajador:</label>
            <input type="text" id="searchWorker" class="form-control" placeholder="Ingrese el nombre del trabajador" onkeyup="searchWorker()">
            <div id="searchResult" class="mt-2"></div> <!-- Aquí aparecerán los resultados -->
        </div>

        <!-- Control de receso del trabajador -->
        <div id="main-worker" class="worker-box">
            <h4 id="worker-name">Trabajador 1</h4>
            <div id="timer-1" class="timer in-time">15:00</div>
            <button class="btn btn-success" onclick="startBreak(1)">Iniciar Receso</button>
            <button class="btn btn-danger" onclick="endBreak(1)" disabled>Finalizar Receso</button>
        </div>

        <!-- Bootstrap JS and Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Script para el cronómetro -->
        <script>
            let timers = {};

            // Iniciar el cronómetro
            function startBreak(workerId) {
                document.querySelector(`#timer-${workerId}`).classList.remove('over-time');
                document.querySelector(`#timer-${workerId}`).classList.add('in-time');
                document.querySelector(`button[onclick="startBreak(${workerId})"]`).disabled = true;
                document.querySelector(`button[onclick="endBreak(${workerId})"]`).disabled = false;

                let timeLeft = 15 * 60; // 15 minutos en segundos
                timers[workerId] = setInterval(() => {
                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;
                    seconds = seconds < 10 ? '0' + seconds : seconds;
                    document.getElementById(`timer-${workerId}`).textContent = `${minutes}:${seconds}`;

                    if (timeLeft <= 0) {
                        document.querySelector(`#timer-${workerId}`).classList.add('over-time');
                        document.querySelector(`#timer-${workerId}`).classList.remove('in-time');
                    }

                    timeLeft--;
                }, 1000);
            }

            // Finalizar receso
            function endBreak(workerId) {
                clearInterval(timers[workerId]);
                document.querySelector(`button[onclick="startBreak(${workerId})"]`).disabled = false;
                document.querySelector(`button[onclick="endBreak(${workerId})"]`).disabled = true;

                // Aquí puedes enviar los datos al servidor (PHP) para registrar la hora de finalización
            }

            // Reloj en tiempo real
            function startClock() {
                setInterval(() => {
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
                }, 1000);
            }

            // Buscar trabajador y autocompletar
            function searchWorker() {
                const query = document.getElementById('searchWorker').value;
                if (query.length > 2) {
                    // Simulamos una búsqueda rápida
                    const results = ['Juan Pérez', 'Maria Lopez', 'Carlos Gomez'].filter(name => name.toLowerCase().includes(query.toLowerCase()));
                    const resultDiv = document.getElementById('searchResult');
                    resultDiv.innerHTML = results.map(worker => `<div onclick="selectWorker('${worker}')">${worker}</div>`).join('');
                }
            }

            // Autocompletar el nombre del trabajador
            function selectWorker(name) {
                document.getElementById('worker-name').textContent = name;
                document.getElementById('searchResult').innerHTML = ''; // Limpiar los resultados
                document.getElementById('searchWorker').value = ''; // Limpiar el cuadro de búsqueda
            }

            // Inicializar reloj en tiempo real
            startClock();
        </script>
</body>

</html>