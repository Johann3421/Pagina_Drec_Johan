let recesosActivos = {};
let numeroFila = 1;

// Registrar receso y generar fila en la tabla
// Registrar receso y generar fila en la tabla
function registrarReceso() {
    const id = document.getElementById('worker-id').value;
    const nombre = document.getElementById('worker-name').value;
    const dni = document.getElementById('dniWorker').value;
    const duracion = document.getElementById('recesoDuration').value;

    if (!id || !nombre || !dni || !duracion) {
        alert("Por favor, complete todos los campos antes de iniciar el receso.");
        return;
    }

    // Enviar los datos al servidor para insertar la hora de receso
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

            // Guardar los datos en localStorage
            localStorage.setItem(`receso_${id}`, JSON.stringify({ horaReceso, duracion }));

            // Código para añadir la nueva fila en la tabla
            const newRow = `
            <tr id="fila_${id}">
                <td>${numeroFila++}</td>
                <td>${nombre}</td>
                <td>${dni}</td>
                <td>${horaReceso}</td>
                <td>N/A</td>
                <td>
                    <span id="contador-${id}" class="contador contador-verde">${duracion}:00</span>
                </td>
                <td>
                    <button class='btn btn-danger' onclick='finalizarReceso(${id})'>Pausar Receso</button>
                </td>
            </tr>`;

            document.getElementById('tbody-visitas').insertAdjacentHTML('beforeend', newRow);
            iniciarContador(id, horaReceso, duracion); // Iniciar el contador en base a horaReceso y duracion
        } else {
            alert(data.message || "Hubo un error al registrar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}

// Finalizar el receso y actualizar la tabla
function finalizarReceso(id) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    // Comprobar si el contador ha llegado a 0 o si está activo
    if (contadorElemento.textContent === "00:00") {
        alert("El tiempo de receso ya ha terminado para este trabajador.");
        return;
    }

    // Enviar el ID del trabajador al servidor para registrar la hora de vuelta
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
                fila.cells[4].textContent = horaVuelta; // Columna de hora de vuelta
            }

            // Limpiar el intervalo del contador y eliminarlo del objeto de recesos activos
            clearInterval(recesosActivos[id]);
            delete recesosActivos[id];

            // Remover la fila de la tabla tras un breve retraso (opcional)
            setTimeout(() => fila.remove(), 1000);
        } else {
            alert(data.message || "Hubo un error al finalizar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para iniciar el contador
function iniciarContador(id, horaReceso, duracionMinutos) {
    const contadorElemento = document.getElementById(`contador-${id}`);

    // Revisar si el receso está en localStorage
    const recesoGuardado = JSON.parse(localStorage.getItem(`receso_${id}`));
    if (recesoGuardado) {
        horaReceso = recesoGuardado.horaReceso;
        duracionMinutos = recesoGuardado.duracion;
    }

    // Convertir la hora de receso a objeto Date y calcular la hora de finalización
    const inicioReceso = new Date(horaReceso);
    const finReceso = new Date(inicioReceso.getTime() + duracionMinutos * 60000);

    // Iniciar el intervalo para actualizar el contador
    recesosActivos[id] = setInterval(() => {
        const ahora = new Date();
        const tiempoRestante = Math.floor((finReceso - ahora) / 1000); // Tiempo restante en segundos

        if (tiempoRestante > 0) {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            contadorElemento.textContent = `${minutos < 10 ? '0' : ''}${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
        } else {
            // Cambiar el contador a rojo cuando el tiempo de receso ha terminado
            contadorElemento.classList.remove('contador-verde');
            contadorElemento.classList.add('contador-rojo');
            clearInterval(recesosActivos[id]);
            contadorElemento.textContent = "00:00";
            localStorage.removeItem(`receso_${id}`); // Limpiar el receso del almacenamiento
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