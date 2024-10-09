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

// Obtener los datos del formulario
$dni = $_POST['dni'];
$nombre = $_POST['nombre'];
$tipopersona = $_POST['tipopersona'];
$institucion = $_POST['institucion'];
$nomoficina = $_POST['nomoficina'];
$smotivo = $_POST['smotivo'];
$lugar = $_POST['lugar'];
$hora_ingreso = date('H:i:s');  // Hora actual en Perú

// Insertar los datos en la base de datos
$sql = "INSERT INTO visitas (dni, nombre, tipopersona, institucion, nomoficina, smotivo, lugar, hora_ingreso)
        VALUES ('$dni', '$nombre', '$tipopersona', '$institucion', '$nomoficina', '$smotivo', '$lugar', '$hora_ingreso')";

if ($conn->query($sql) === TRUE) {
    echo "Registro insertado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

// Redirigir de vuelta al formulario (opcional)
header("Location: welcome.php");
?>
