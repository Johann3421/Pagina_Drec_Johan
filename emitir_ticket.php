<?php
// emitir_ticket.php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Aquí puedes procesar los datos del ticket como necesites.
// Por ejemplo, podrías guardarlo en una base de datos.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $descripcion = $_POST['descripcion'];

    // Simulación de almacenamiento del ticket
    // En este punto podrías guardar los datos en una base de datos.
    echo "<h1>Ticket generado con éxito!</h1>";
    echo "<p>Nombre: $nombre $apellidoPaterno $apellidoMaterno</p>";
    echo "<p>Descripción: $descripcion</p>";
}
?>

<a href="welcome.php" class="btn btn-primary">Volver</a>
