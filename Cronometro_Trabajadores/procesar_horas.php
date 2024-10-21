<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos enviados por fetch
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$tipo = $data['tipo'];

if ($tipo === 'receso') {
    $hora_receso = date('Y-m-d H:i:s'); // Hora actual
    $sql = "UPDATE trabajadores SET hora_receso = '$hora_receso' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'hora_receso' => $hora_receso]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} elseif ($tipo === 'vuelta') {
    $hora_vuelta = date('Y-m-d H:i:s'); // Hora de vuelta
    $sql = "UPDATE trabajadores SET hora_vuelta = '$hora_vuelta' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'hora_vuelta' => $hora_vuelta]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
