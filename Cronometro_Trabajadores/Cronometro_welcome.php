<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Receso de Trabajadores</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .timer {
            font-size: 24px;
            font-weight: bold;
        }
        .over-time {
            color: red; /* Cuando el tiempo sobrepasa los 15 minutos */
        }
        .in-time {
            color: green; /* Mientras esté en tiempo permitido */
        }
        .worker-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .small-box {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 250px;
            z-index: 9999;
            background-color: #f8f9fa;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.2);
        }
        #clock {
            font-size: 36px;
            text-align: center;
            margin-bottom: 30px;
        }
        @media (max-width: 768px) {
            .worker-box {
                text-align: center;
            }
        }
    </style>
</head>
<body class="container my-5">
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

            let timeLeft = 15 * 60;  // 15 minutos en segundos
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
                // Aquí debería hacerse una búsqueda en la base de datos a través de AJAX
                // Simulamos una búsqueda rápida
                const results = ['Juan Pérez', 'Maria Lopez', 'Carlos Gomez'].filter(name => name.toLowerCase().includes(query.toLowerCase()));

                const resultDiv = document.getElementById('searchResult');
                resultDiv.innerHTML = results.map(worker => `<div onclick="selectWorker('${worker}')">${worker}</div>`).join('');
            }
        }

        // Autocompletar el nombre del trabajador
        function selectWorker(name) {
            document.getElementById('worker-name').textContent = name;
            document.getElementById('searchResult').innerHTML = '';  // Limpiar los resultados
            document.getElementById('searchWorker').value = '';      // Limpiar el cuadro de búsqueda
        }

        startClock();
    </script>
</body>
</html>
