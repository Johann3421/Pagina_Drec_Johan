<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "login_system");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $workerId = $_POST['workerId'];
    $action = $_POST['action'];

    if ($action == 'start') {
        // Registrar la hora de inicio
        $sql = "INSERT INTO recesos (worker_id, inicio) VALUES (?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $workerId);
        $stmt->execute();
    } else if ($action == 'end') {
        // Registrar la hora de fin
        $sql = "UPDATE recesos SET fin = NOW() WHERE worker_id = ? AND fin IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $workerId);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}
?>
