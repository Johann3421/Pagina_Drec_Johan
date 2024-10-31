let recesosActivos = {};
let numeroFila = 1;
let tiemposRestantes = {};

function registrarReceso() {
    const id = document.getElementById('worker-id').value;
    const nombre = document.getElementById('worker-name').value;
    const dni = document.getElementById('dniWorker').value;
    const duracion = document.getElementById('recesoDuration').value;

    if (!id || !nombre || !dni || !duracion) {
        alert("Por favor, complete todos los campos antes de iniciar el receso.");
        return;
    }

    fetch('registrar_receso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id, duracion })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const horaReceso = data.hora_receso;
            const newRow = `
                <tr id="fila_${id}">
                    <td>${document.querySelectorAll('#tbody-visitas tr').length + 1}</td>
                    <td>${nombre}</td>
                    <td>${dni}</td>
                    <td>${horaReceso}</td>
                    <td>N/A</td>
                    <td>
                        <span id="contador-${id}" class="contador contador-verde">${duracion}:00</span>
                    </td>
                    <td>
                        <button class="btn btn-danger" onclick="finalizarReceso(${id})">
                            <i class="fas fa-stop"></i> Finalizar
                        </button>
                    </td>
                </tr>`;
            document.getElementById('tbody-visitas').insertAdjacentHTML('beforeend', newRow);

            iniciarContador(id, duracion * 60); // Iniciar el contador
        } else {
            alert(data.message || "Hubo un error al registrar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para finalizar el receso
function finalizarReceso(id) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    if (!contadorElemento || contadorElemento.textContent === "00:00") {
        alert("El receso ya ha terminado o no está activo.");
        return;
    }

    fetch('finalizar_receso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const horaVuelta = data.hora_vuelta;

            const fila = document.getElementById(`fila_${id}`);
            if (fila) {
                fila.cells[4].textContent = horaVuelta;
            }

            clearInterval(recesosActivos[id]);
            delete recesosActivos[id];
            contadorElemento.textContent = "00:00";
            localStorage.removeItem(`receso_${id}`);

            setTimeout(() => fila.remove(), 1000);
        } else {
            alert(data.message || "Hubo un error al finalizar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para alternar entre Pausar y Reanudar el contador
function alternarPausaReanudar(id, boton) {
    const icono = boton.querySelector('i');
    const contadorElemento = document.getElementById(`contador-${id}`);
    const pausado = boton.getAttribute('data-pausado') === 'true'; // Verificar estado actual

    if (pausado) {
        // Reanudar el contador
        const tiempoRestante = tiemposRestantes[id];
        iniciarContador(id, tiempoRestante);

        // Cambiar a "Pausar" con ícono de pausa y color amarillo
        boton.innerHTML = '<i class="fas fa-pause"></i> Pausar';
        boton.classList.remove('btn-success');
        boton.classList.add('btn-warning');
        boton.setAttribute('data-pausado', 'false'); // Cambiar estado
    } else {
        // Pausar el contador
        clearInterval(recesosActivos[id]);
        delete recesosActivos[id];

        // Guardar tiempo restante
        const [minutos, segundos] = contadorElemento.textContent.split(':').map(Number);
        tiemposRestantes[id] = minutos * 60 + segundos;

        // Cambiar a "Reanudar" con ícono de play y color verde
        boton.innerHTML = '<i class="fas fa-play"></i> Reanudar';
        boton.classList.remove('btn-warning');
        boton.classList.add('btn-success');
        boton.setAttribute('data-pausado', 'true'); // Cambiar estado
    }
}


function iniciarContadores() {
    fetch('calcular_tiempo_restante.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            data.trabajadores.forEach(trabajador => {
                iniciarContador(trabajador.id, trabajador.tiempo_restante, trabajador.en_tiempo_extra);
            });
        } else {
            console.error("Error al obtener los tiempos restantes:", data.message);
        }
    })
    .catch(error => console.error('Error en la solicitud de tiempos restantes:', error));
}

function iniciarContador(id, tiempoRestante, enTiempoExtra = false) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    if (!contadorElemento) {
        console.error(`Contador no encontrado para el trabajador ${id}.`);
        return;
    }

    if (enTiempoExtra) {
        contadorElemento.classList.remove('contador-verde');
        contadorElemento.classList.add('contador-rojo');
    } else {
        contadorElemento.classList.remove('contador-rojo');
        contadorElemento.classList.add('contador-verde');
    }

    recesosActivos[id] = setInterval(() => {
        if (!enTiempoExtra && tiempoRestante > 0) {
            // Contador verde mientras hay tiempo restante
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            contadorElemento.textContent = 
                `${minutos < 10 ? '0' : ''}${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
            tiempoRestante--;
        } else {
            // Cambiar a tiempo extra positivo desde 00:00
            if (!enTiempoExtra) {
                contadorElemento.classList.remove('contador-verde');
                contadorElemento.classList.add('contador-rojo');
                tiempoRestante = 0;
                enTiempoExtra = true;
            }

            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            contadorElemento.textContent = 
                `${minutos < 10 ? '0' : ''}${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
            tiempoRestante++; // Contar en positivo
        }
    }, 1000);
}

// Actualización del reloj digital
function actualizarRelojDigital() {
    const reloj = document.querySelector('.digital-clock .time');
    const ahora = new Date();
    reloj.querySelector('.hour').textContent = String(ahora.getHours()).padStart(2, '0');
    reloj.querySelector('.minute').textContent = String(ahora.getMinutes()).padStart(2, '0');
    reloj.querySelector('.second').textContent = String(ahora.getSeconds()).padStart(2, '0');
    reloj.querySelector('.ampm').textContent = ahora.getHours() >= 12 ? 'PM' : 'AM';
}

setInterval(actualizarRelojDigital, 1000);

// Búsqueda de trabajadores
function buscarTrabajador() {
    let query = document.getElementById('searchWorker').value;
    if (query.length > 2) {
        fetch(`buscar_trabajador.php?busqueda=${query}`)
            .then(response => response.text())
            .then(data => document.getElementById('searchResult').innerHTML = data);
    } else {
        document.getElementById('searchResult').innerHTML = '';
    }
}


// Seleccionar trabajador de la búsqueda y autocompletar los campos
function seleccionarTrabajador(id, nombre, dni) {
    document.getElementById('worker-id').value = id;
    document.getElementById('worker-name').value = nombre;
    document.getElementById('dniWorker').value = dni;
    document.getElementById('worker-name-display').textContent = nombre;
    document.getElementById('searchWorker').value = nombre;
    document.getElementById('searchResult').innerHTML = ''; // Limpiar resultados de búsqueda
}