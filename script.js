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

  function noSubmitEnter(event) {
    if (event.key === "Enter") {
      event.preventDefault(); // Evitar que se envíe el formulario
      return false;
    }
    return true;
  }

  // Validar el formulario antes de enviarlo
  function validarFormulario() {
    var dni = document.getElementById("ndocu").value;
    var nombre = document.getElementById("nombre").value;

    // Limpiar errores previos
    document.getElementById("dni_error").innerHTML = "";
    document.getElementById("nombre_error").innerHTML = "";

    // Validar DNI
    if (dni === "" || dni.length !== 8) {
      document.getElementById("dni_error").innerHTML = "El DNI debe tener 8 dígitos.";
      return false; // No envíes el formulario
    }

    // Validar Nombres y Apellidos
    if (nombre === "") {
      document.getElementById("nombre_error").innerHTML = "El nombre es obligatorio.";
      return false; // No envíes el formulario
    }

    // Si todas las validaciones pasan, permite el envío
    return true;
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Reporte.php//
  function imprimirTicket(id) {
    window.location.href = "imprimir_ticket.php?id=" + id;
  }