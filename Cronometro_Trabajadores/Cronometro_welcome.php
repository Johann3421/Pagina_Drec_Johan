<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para mostrar trabajadores en receso sin hora de vuelta
$sql = "SELECT id, nombre, dni, hora_receso, hora_vuelta 
        FROM trabajadores 
        WHERE hora_receso IS NOT NULL AND hora_vuelta IS NULL";
$result = $conn->query($sql);

// Verificar si la consulta devolvió resultados
if ($result === false) {
    die("Error en la consulta SQL: " . $conn->error . ". Consulta: " . $sql);
}
?>




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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .header {
            background-color: #00aeff;
            display: flex;
            justify-content: space-between;
            /* Asegura que el logo esté a la izquierda y lo demás a la derecha */
            align-items: center;
            height: 85px;
            padding: 5px 10%;
        }

        .header .logo-container {
            display: flex;
            align-items: center;
        }

        .header .logo {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .header .logo img {
            height: 100px;
            /* Tamaño más grande para el logo */
            width: auto;
            transition: all 0.3s;
            margin-left: -30%;
        }

        .header .logo img:hover {
            transform: scale(1.2);
        }

        .header .logo-text {
            font-size: 24px;
            /* Ajusta el tamaño del texto */
            font-weight: 700;
            color: #fff;
            /* Color del texto */
            margin-left: 15px;
            /* Separación entre el logo y el texto */
            text-transform: uppercase;
            /* Hace que el texto esté en mayúsculas */
        }

        .header .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
        }

        .header .nav-links li {
            display: inline-block;
            padding: 0 20px;
        }

        .header .nav-links li:hover {
            transform: scale(1.1);
        }

        .header .nav-links a {
            font-size: 700;
            color: #000000;
            text-decoration: none;
        }

        .header .nav-links li a:hover {
            color: #ffbc0e;
        }

        .header .btn-1 button {
            margin-left: 20px;
            font-weight: 700;
            color: #1b3039;
            padding: 9px 25px;
            background: #eceff1;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease 0s;
        }

        .header .btn-1 button:hover {
            background-color: #e2f1f8;
            color: #ffbc0e;
            transform: scale(1.1);
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

        /* Layout for main content */


        /* Flexbox for the left and right sections */
        .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .left-section,
        .right-section {
            width: 48%;
            margin-bottom: 20px;
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
            width: 250px;
            height: 250px;
            margin-bottom: 20px;
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

            .left-section,
            .right-section {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        .digital-clock {
            background: #2f363e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 10px 50px 70px rgba(0, 0, 0, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 300px;
            height: 150px;
        }

        .digital-clock::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: #2f363e;
            border: 3px solid #fff;
            border-radius: 50%;
            z-index: 1;
        }

        .time {
            color: #fff;
            font-size: 50px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .time span {
            color: #04fc43;
            font-weight: 600;
            text-shadow: 0 0 20px #0b9be8, 0 0 60px #04fc43;
        }

        .time .hour {
            color: #ff2972;
        }

        .time .minute {
            color: #fee800;
        }

        .time .second {
            color: #04fc43;
        }

        .time .ampm {
            color: #fff;
            font-size: 20px;
            position: relative;
            top: 15px;
        }

        /* Asegurarse de que los estilos sean visualmente claros */
        .contador {
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <div class="logo">
                    <img src="../imagenes/logo_dre.png" alt="Logo de la marca" class="brand-image img-circle elevation-3">
                    <span class="brand-text font-weight-light">DRE-HUÁNUCO</span>
                </div>
            </a>

            <!-- Sidebar Menu -->
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
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <header class="header">
                <div class="logo">
                    <img src="../imagenes/logo_dre.png" alt="Logo de la marca">
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

            <!-- Main container -->
            <section class="content">
                <div id="myModal" class="modal fade" role="dialog"></div>
                <div class="row">
                    <!-- Left section: Search worker -->
                    <div class="left-section">
                        <div class="search-worker mb-3">
                            <label for="searchWorker" class="form-label">Buscar Trabajador:</label>
                            <input type="text" id="searchWorker" class="form-control" placeholder="Ingrese el nombre del trabajador" onkeyup="buscarTrabajador()">
                            <div id="searchResult" class="mt-2"></div>
                        </div>

                        <!-- Campos ocultos para almacenar el ID del trabajador -->
                        <input type="hidden" id="worker-id">
                        <input type="hidden" id="worker-name">
                        <input type="hidden" id="worker-dni">

                        <!-- Control de receso -->
                        <div id="main-worker" class="worker-box">
                            <h4 id="worker-name-display">Nombre del Trabajador</h4> <!-- Aquí se autocompletará el nombre -->
                            <div class="search-worker mb-3">
                                <label for="dniWorker" class="form-label">DNI:</label>
                                <input type="text" id="dniWorker" class="form-control" placeholder="Ingrese el DNI" maxlength="8" readonly>
                            </div>
                            <div id="timer-1" class="timer in-time">5:00</div>
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="registrarHora(document.getElementById('worker-id').value, 'receso')">Iniciar Receso</button>
                            </div>
                        </div>
                    </div>

                    <!-- Right section: Clock -->
                    <div class="right-section">
                        <div class="clock-container">
                            <!-- Reloj Analógico -->

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

                <!-- Tabla de visitas -->
                <div class="abajo">
                    <table id="tblvisita" class="table display table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nro.</th>
                                <th>Trabajador</th>
                                <th>Documento</th>
                                <th>Hora de Receso</th>
                                <th>Hora de Vuelta</th>
                                <th>Tiempo (Contador)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-visitas">
                            <tr>
                                <td colspan="7">No hay datos disponibles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>




            </section>

            <!-- Footer -->

        </div>
    </div>
</body>



<!-- Bootstrap JS and Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para el cronómetro -->
<script>
    function buscarPorDNI() {
        var dni = document.getElementById("dniWorker").value;

        if (dni.length === 8) {
            var token = 'tu-token-api-aqui';

            fetch(`https://dniruc.apisperu.com/api/v1/dni/${dni}?token=${token}`)
                .then(response => response.json())
                .then(data => {
                    if (data.nombres) {
                        document.getElementById("worker-name").textContent = `${data.nombres} ${data.apellidoPaterno} ${data.apellidoMaterno}`;
                        document.getElementById("dni-error").textContent = ""; // Limpiar cualquier error
                    } else {
                        document.getElementById("worker-name").textContent = "No se encontró el trabajador";
                        document.getElementById("dni-error").textContent = "DNI no válido o no encontrado.";
                    }
                })
                .catch(error => {
                    console.error("Error al consultar el DNI:", error);
                    document.getElementById("dni-error").textContent = "Error en la consulta. Intente nuevamente.";
                });
        } else if (dni.length > 0 && dni.length < 8) {
            document.getElementById("dni-error").textContent = "El DNI debe tener 8 dígitos.";
        } else {
            document.getElementById("dni-error").textContent = ""; // Limpiar errores si no hay problemas
        }
    }

    // Buscar trabajadores en tiempo real
    function buscarTrabajador() {
        let query = document.getElementById('searchWorker').value;
        if (query.length > 2) {
            fetch(`buscar_trabajador.php?busqueda=${query}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('searchResult').innerHTML = data;
                });
        } else {
            document.getElementById('searchResult').innerHTML = '';
        }
    }

    // Seleccionar trabajador de la búsqueda y autocompletar los campos
    function seleccionarTrabajador(id, nombre, dni) {
        // Autocompletar campos ocultos y visibles
        document.getElementById('worker-id').value = id;
        document.getElementById('worker-name').value = nombre;
        document.getElementById('dniWorker').value = dni;
        document.getElementById('worker-name-display').textContent = nombre;
        document.getElementById('searchWorker').value = nombre; // Este campo se llena con el nombre del trabajador buscado
        document.getElementById('searchResult').innerHTML = ''; // Limpiar resultados de búsqueda
    }



    function iniciarContador(id) {
        let contadorElement = document.getElementById(`contador-${id}`);
        let time = 5 * 60; // 5 minutos en segundos

        timers[id] = setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            contadorElement.textContent = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            time--;

            if (time < 0) {
                clearInterval(timers[id]);
                contadorElement.textContent = "Tiempo terminado";
            }
        }, 1000); // Actualizar cada segundo
    }

    function pausarReceso(id) {
        if (timers[id]) {
            clearInterval(timers[id]); // Pausar el contador
            delete timers[id];
            alert("Receso pausado");
        }
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

    // Registrar la hora de receso o vuelta
    function registrarHora(id, tipo) {
        const url = 'procesar_horas.php';
        const data = {
            id: id,
            tipo: tipo
        };

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    if (tipo === 'receso') {
                        // Actualizar la tabla con la hora de receso sin eliminar las filas existentes
                        const workerName = document.getElementById('worker-name').value;
                        const workerDni = document.getElementById('dniWorker').value;
                        const newRow = `
                    <tr id="fila_${id}">
                        <td>${id}</td>
                        <td>${workerName}</td>
                        <td>${workerDni}</td>
                        <td id="hora_receso_${id}">${result.hora_receso}</td>
                        <td id="hora_vuelta_${id}">N/A</td>
                        <td><span id="contador-${id}">05:00</span></td>
                        <td>
                            <button class='btn btn-danger' onclick='registrarHora(${id}, "vuelta")'>Pausar Receso</button>
                        </td>
                    </tr>`;
                        document.querySelector("#tblvisita tbody").insertAdjacentHTML('beforeend', newRow);
                    } else if (tipo === 'vuelta') {
                        // Actualizar la fila con la hora de vuelta
                        document.getElementById(`hora_vuelta_${id}`).textContent = result.hora_vuelta;

                        // Opcionalmente, ocultar la fila después de registrar la hora de vuelta
                        document.getElementById(`fila_${id}`).style.display = 'none';
                    }
                } else {
                    alert('Error al registrar la hora.');
                }
            })
            .catch(error => {
                console.error('Error al registrar la hora:', error);
            });
    }

    // Objeto global para almacenar los intervalos de cada trabajador
const timers = {};

// Registrar la hora de receso o vuelta
function registrarHora(id, tipo) {
    const url = 'procesar_horas.php';
    const data = { id, tipo };

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                if (tipo === 'receso') {
                    actualizarTabla(id, result.hora_receso);
                    iniciarContador(id); // Iniciar el contador al registrar receso
                } else if (tipo === 'vuelta') {
                    document.getElementById(`hora_vuelta_${id}`).textContent = result.hora_vuelta;
                }
            } else {
                alert('Error al registrar la hora.');
            }
        })
        .catch(error => console.error('Error al registrar la hora:', error));
}

// Actualizar la tabla con los datos del receso
function actualizarTabla(id, horaReceso) {
    const workerName = document.getElementById('worker-name').value;
    const workerDni = document.getElementById('dniWorker').value;

    const newRow = `
        <tr id="fila_${id}">
            <td>${id}</td>
            <td>${workerName}</td>
            <td>${workerDni}</td>
            <td id="hora_receso_${id}">${horaReceso}</td>
            <td id="hora_vuelta_${id}">N/A</td>
            <td><span id="contador-${id}" class="contador">05:00</span></td>
            <td>
                <button class='btn btn-danger' onclick='registrarHora(${id}, "vuelta")'>Pausar Receso</button>
            </td>
        </tr>`;
    document.getElementById('tbody-visitas').innerHTML += newRow;
}

// Iniciar el contador para un trabajador
function iniciarContador(id) {
    const contadorElemento = document.getElementById(`contador-${id}`);
    let tiempoRestante = 5 * 60; // 5 minutos en segundos

    // Limpiar cualquier intervalo anterior
    if (timers[id]) {
        clearInterval(timers[id]);
    }

    // Iniciar un nuevo intervalo
    timers[id] = setInterval(() => {
        const minutos = Math.floor(tiempoRestante / 60);
        const segundos = tiempoRestante % 60 < 10 ? `0${tiempoRestante % 60}` : tiempoRestante % 60;

        contadorElemento.textContent = `${minutos}:${segundos}`;

        // Cambiar color según tiempo restante
        if (tiempoRestante >= 0) {
            contadorElemento.style.backgroundColor = 'lightgreen';
        } else {
            contadorElemento.style.backgroundColor = 'red';
        }

        tiempoRestante--;

        if (tiempoRestante < -1) {
            clearInterval(timers[id]);
            contadorElemento.textContent = "Tiempo terminado";
        }
    }, 1000);
}

// Pausar el receso y mostrar un popup si se excede el tiempo
function pausarReceso(id) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    if (timers[id]) {
        clearInterval(timers[id]);
        delete timers[id];
    }

    const [minutos, segundos] = contadorElemento.textContent.split(':').map(Number);
    const tiempoRestante = minutos * 60 + segundos;

    if (tiempoRestante < 0) {
        const tiempoPasado = Math.abs(tiempoRestante);
        const minutosPasados = Math.floor(tiempoPasado / 60);
        const segundosPasados = tiempoPasado % 60 < 10 ? `0${tiempoPasado % 60}` : tiempoPasado % 60;

        alert(`El receso se ha pasado por ${minutosPasados}:${segundosPasados} minutos.`);
    }
}
</script>



</html>