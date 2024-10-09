<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';  // Conexión a la base de datos

    // Configurar la zona horaria para Perú
    date_default_timezone_set('America/Lima');

    // Obtener el ID de la visita
    $id = $_POST['id'];
    $horaSalida = date('H:i:s');  // Registrar la hora actual como hora de salida

    // Actualizar la visita con la hora de salida
    $sql = "UPDATE visitas SET hora_salida = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $horaSalida, $id);

    if ($stmt->execute()) {
        echo "Salida registrada correctamente.";
    } else {
        echo "Error al registrar la salida: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
