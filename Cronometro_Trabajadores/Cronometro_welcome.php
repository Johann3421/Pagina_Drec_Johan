<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Receso de Trabajadores</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE Styles -->
    <link rel="stylesheet" href="https://gestionportales.regionhuanuco.gob.pe/dist/css/adminlte.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* Global styles */
        body {
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', sans-serif;
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #1367C8;
            color: white;
        }

        .header .logo img {
            height: 50px;
        }

        .header .logo-text {
            font-size: 1.2rem;
            font-weight: bold;
            margin-left: 10px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links li {
            list-style: none;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
        }

        .btn {
            background-color: white;
            color: #1367C8;
            border-radius: 4px;
            padding: 5px 15px;
        }

        /* Layout and container for main content */
        .container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .left-section,
        .right-section {
            width: 45%;
        }

        .search-worker {
            margin-bottom: 40px;
        }

        #searchWorker {
            width: 100%;
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

        /* Clock section styling */
        .clock-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .clock {
            position: relative;
            width: 200px;
            height: 200px;
        }

        .clock .circle {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .digital-clock {
            margin-top: 20px;
            font-size: 24px;
        }

        .digital-clock .time {
            font-weight: bold;
        }

        /* Break timer box */
        .worker-box {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .worker-box h4 {
            text-align: center;
            font-weight: bold;
            color: #1367C8;
        }

        .timer {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        /* Informe section styling */
        .report-box {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .report-box h4 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-section,
            .right-section {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Header -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/" class="brand-link">
        <div class="logo">
          <img src="../imagenes/logo_dre.png" alt="Logo de la marca" class="brand-image img-circle elevation-3">
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
          </ul>
        </nav>
      </div>
    </aside>
     
    <div class="content-wrapper" style="min-height: 678.031px;">
    <header class="header">
        <div class="logo">
            <img src="../imagenes/logo_dre.png" alt="Logo de la marca">
            <span class="logo-text">DRE-HUÁNUCO</span>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="welcome.php">Registrar Visita</a></li>
                <li><a href="reporte.php">Reporte</a></li>
                <li><a href="./Cronometro_Trabajadores/Cronometro_welcome.php">Cronometro</a></li>
            </ul>
        </nav>
        <a class="btn" href="https://www.drehuanuco.gob.pe/">
            <button>Pagina Oficial</button>
        </a>
    </header>

    <!-- Main container -->
    <div class="container">
        <!-- Left section: Search worker -->
        <div class="left-section">
            <div class="search-worker">
                <label for="searchWorker" class="form-label">Buscar Trabajador:</label>
                <input type="text" id="searchWorker" class="form-control" placeholder="Ingrese el nombre del trabajador" onkeyup="searchWorker()">
                <div id="searchResult" class="mt-2"></div>
            </div>

            <!-- Control de receso del trabajador -->
            <div id="main-worker" class="worker-box">
                <h4 id="worker-name">Trabajador 1</h4>
                <div id="timer-1" class="timer in-time">15:00</div>
                <div class="btn-group">
                    <button class="btn btn-success" onclick="startBreak(1)">Iniciar Receso</button>
                    <button class="btn btn-danger" onclick="endBreak(1)" disabled>Finalizar Receso</button>
                </div>
            </div>
        </div>

        <!-- Right section: Clock -->
        <div class="right-section">
            <div class="clock-container">
                <!-- Reloj Analógico -->
                <div class="clock">
                    <div class="circle" id="sc" style="--clr:#04fc43;"><i></i></div>
                    <div class="circle circle2" id="mn" style="--clr:#fee800;"><i></i></div>
                    <div class="circle circle3" id="hr" style="--clr:#ff2972;"><i></i></div>
                    <span style="--i:1;"><b>1</b></span>
                    <span style="--i:2;"><b>2</b></span>
                    <span style="--i:3;"><b>3</b></span>
                    <span style="--i:4;"><b>4</b></span>
                    <span style="--i:5;"><b>5</b></span>
                    <span style="--i:6;"><b>6</b></span>
                    <span style="--i:7;"><b>7</b></span>
                    <span style="--i:8;"><b>8</b></span>
                    <span style="--i:9;"><b>9</b></span>
                    <span style="--i:10;"><b>10</b></span>
                    <span style="--i:11;"><b>11</b></span>
                    <span style="--i:12;"><b>12</b></span>
                </div>

                <!-- Reloj Digital -->
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

    <!-- Informe de receso -->
    <div class="container mt-5">
        <div class="report-box">
            <h4>Informe de Receso</h4>
            <p>Hora de inicio: <span id="start-time">10:00 AM</span></p>
            <p>Tiempo de receso restante: <span id="remaining-time">5:00</span></p>
        </div>
    </div>
    </div>
    <footer class="main-footer">
      <strong>&copy; 2024 <a href="#">Portalweb</a>.</strong> Todos los derechos reservados.
    </footer>
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

            let hr = document.querySelector('#hr');
            let mn = document.querySelector('#mn');
            let sc = document.querySelector('#sc');

            setInterval(() => {
                let day = new Date();
                let hh = day.getHours() * 30;
                let mm = day.getMinutes() * 6;
                let ss = day.getSeconds() * 6;

                // Corrección de comillas simples a backticks
                hr.style.transform = `rotateZ(${hh + (mm / 12)}deg)`;
                mn.style.transform = `rotateZ(${mm}deg)`; // Aquí era "mn" en vez de "mm"
                sc.style.transform = `rotateZ(${ss}deg)`;
            }, 1000); // Actualiza cada segundo
            function updateClock() {
                let now = new Date();
                let hours = now.getHours();
                let minutes = now.getMinutes();
                let seconds = now.getSeconds();
                let ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12 || 12;

                // Add leading zeros to minutes and seconds
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                document.querySelector('.hour').textContent = hours;
                document.querySelector('.minute').textContent = minutes;
                document.querySelector('.second').textContent = seconds;
                document.querySelector('.ampm').textContent = ampm;
            }

            setInterval(updateClock, 1000);
            updateClock(); // Initialize clock immediately
        </script>

</body>

</html>