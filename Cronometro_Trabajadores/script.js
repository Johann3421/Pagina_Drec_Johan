let recesosActivos = {};
let numeroFila = 1;

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
        body: new URLSearchParams({
            id: id,
            duracion: duracion
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const horaReceso = data.hora_receso;

            // Insertar una nueva fila en la tabla
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
                        <button class="btn btn-danger" onclick="finalizarReceso(${id})">Pausar Receso</button>
                    </td>
                </tr>`;

            document.getElementById('tbody-visitas').insertAdjacentHTML('beforeend', newRow);
            const noDataRow = document.querySelector('#tbody-visitas .no-data');
            if (noDataRow) noDataRow.remove();

            // Iniciar el contador para la nueva fila
            iniciarContador(id, duracion * 60); // Pasar duración en segundos
        } else {
            alert(data.message || "Hubo un error al registrar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}



// Finalizar el receso y actualizar la tabla
function finalizarReceso(id) {
    const contadorElemento = document.getElementById(`contador-${id}`);
    
    if (!contadorElemento || contadorElemento.textContent === "00:00") {
        alert("El tiempo de receso ya ha terminado o no está activo.");
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

            // Actualizar la fila correspondiente con la hora de vuelta
            const fila = document.getElementById(`fila_${id}`);
            if (fila) {
                fila.cells[4].textContent = horaVuelta;
            }

            // Limpiar el intervalo del contador y eliminarlo del objeto de recesos activos
            clearInterval(recesosActivos[id]);
            delete recesosActivos[id];
            contadorElemento.textContent = "00:00";
            localStorage.removeItem(`receso_${id}`); // Limpiar el receso en localStorage
            setTimeout(() => fila.remove(), 1000); // Opcional: remover la fila después
        } else {
            alert(data.message || "Hubo un error al finalizar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}



// Función para cargar y mostrar contadores en base al tiempo restante del servidor
function iniciarContadores() {
    // Consultar el tiempo restante de todos los recesos activos desde el servidor
    fetch('calcular_tiempo_restante.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Para cada trabajador con receso activo, iniciamos el contador
            data.trabajadores.forEach(trabajador => {
                iniciarContador(trabajador.id, trabajador.tiempo_restante);
            });
        } else {
            console.error("Error al obtener los tiempos restantes:", data.message);
        }
    })
    .catch(error => console.error('Error en la solicitud de tiempos restantes:', error));
}

// Función para iniciar un contador individual con un tiempo restante específico
function iniciarContador(id, tiempoRestante) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    if (!contadorElemento) {
        console.error(`Elemento contador-${id} no encontrado.`);
        return;
    }

    // Asegurar que el contador empiece en verde
    contadorElemento.classList.remove('contador-rojo');
    contadorElemento.classList.add('contador-verde');

    const intervalo = setInterval(() => {
        if (tiempoRestante > 0) {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            contadorElemento.textContent = `${minutos < 10 ? '0' : ''}${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
            tiempoRestante--;
        } else {
            // Cambiar a rojo cuando el tiempo se agote
            contadorElemento.classList.remove('contador-verde');
            contadorElemento.classList.add('contador-rojo');
            contadorElemento.textContent = "00:00";
            clearInterval(intervalo);
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