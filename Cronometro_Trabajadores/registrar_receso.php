<?php
// Establecer la zona horaria a la de Perú
date_default_timezone_set('America/Lima');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "login_system");

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]));
    }

    // Recibir datos de la solicitud POST
    $workerId = $_POST['workerId'] ?? null;
    $action = $_POST['action'] ?? null;

    // Verificar que los parámetros requeridos existen
    if (!$workerId || !$action) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        $conn->close();
        exit;
    }

    // Procesar la acción recibida ('start' para inicio de receso, 'end' para finalizarlo)
    if ($action === 'start') {
        // Registrar la hora de inicio del receso en la zona horaria de Perú
        $sql = "INSERT INTO recesos (worker_id, inicio) VALUES (?, NOW())";
    } elseif ($action === 'end') {
        // Registrar la hora de fin del receso (solo si está abierto, es decir, fin IS NULL)
        $sql = "UPDATE recesos SET fin = NOW() WHERE worker_id = ? AND fin IS NULL";
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        $conn->close();
        exit;
    }

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $workerId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Receso registrado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el receso']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
    }

    // Cerrar la conexión
    $conn->close();
}
?>
