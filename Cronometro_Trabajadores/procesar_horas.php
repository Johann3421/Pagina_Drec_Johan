<?php
date_default_timezone_set('America/Lima');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$tipo = $data['tipo'];
$duracion = isset($data['duracion']) ? $data['duracion'] : 15; // Duración en minutos, por defecto 15

if ($tipo === 'receso') {
    $hora_receso = date('Y-m-d H:i:s');
    $sql = "UPDATE trabajadores SET hora_receso = '$hora_receso' WHERE id = $id";

    // Insertar el registro en la tabla de reportes
    $sql_reporte = "INSERT INTO reportes_recesos (worker_id, nombre, dni, hora_receso, duracion) 
                    SELECT id, nombre, dni, '$hora_receso', $duracion FROM trabajadores WHERE id = $id";

    if ($conn->query($sql) === TRUE && $conn->query($sql_reporte) === TRUE) {
        echo json_encode(['success' => true, 'hora_receso' => $hora_receso]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} elseif ($tipo === 'vuelta') {
    $hora_vuelta = date('Y-m-d H:i:s');
    $sql = "UPDATE trabajadores SET hora_vuelta = '$hora_vuelta' WHERE id = $id";

    // Actualizar el reporte con la hora de vuelta
    $sql_reporte = "UPDATE reportes_recesos SET hora_vuelta = '$hora_vuelta' WHERE worker_id = $id AND hora_vuelta IS NULL";

    if ($conn->query($sql) === TRUE && $conn->query($sql_reporte) === TRUE) {
        echo json_encode(['success' => true, 'hora_vuelta' => $hora_vuelta]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
