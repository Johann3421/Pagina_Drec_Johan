<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar la zona horaria para Perú
date_default_timezone_set('America/Lima');

// Verificar si los datos del formulario están completos
if (isset($_POST['dni'], $_POST['nombre'], $_POST['tipopersona'], $_POST['smotivo'], $_POST['lugar'])) {
    
    // Obtener los datos del formulario y limpiarlos
    $dni = $conn->real_escape_string($_POST['dni']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipopersona = $conn->real_escape_string($_POST['tipopersona']);
    $smotivo = $conn->real_escape_string($_POST['smotivo']);
    $lugar = $conn->real_escape_string($_POST['lugar']);
    $hora_ingreso = date('H:i:s');  // Hora actual en Perú
    $fecha_visita = date('Y-m-d');  // Fecha actual en Perú

    // Insertar los datos en la base de datos usando prepared statements
    $stmt = $conn->prepare("INSERT INTO visitas (dni, nombre, tipopersona, smotivo, lugar, hora_ingreso, fecha) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssss", $dni, $nombre, $tipopersona, $smotivo, $lugar, $hora_ingreso, $fecha_visita);

    // Ejecutar la consulta y verificar
    if ($stmt->execute()) {
        // Redirigir después de la inserción exitosa
        header("Location: welcome.php?mensaje=Registro exitoso");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Cerrar la declaración preparada
    $stmt->close();
    
} else {
    // Mostrar alerta de error y detener ejecución
    echo "<script type='text/javascript'>
            alert('Error: Faltan datos en el formulario');
            window.history.back(); // Regresa a la página anterior para que el usuario corrija los datos
          </script>";
    exit(); // Detiene el flujo del script PHP
}

// Cerrar la conexión
$conn->close();
?>
