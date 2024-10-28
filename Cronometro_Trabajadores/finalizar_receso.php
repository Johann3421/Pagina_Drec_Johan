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

            // Remover el receso de localStorage
            localStorage.removeItem(`receso_${id}`);

            // Remover la fila de la tabla tras un breve retraso (opcional)
            setTimeout(() => fila.remove(), 1000);
        } else {
            alert(data.message || "Hubo un error al finalizar el receso.");
        }
    })
    .catch(error => console.error('Error:', error));
}
