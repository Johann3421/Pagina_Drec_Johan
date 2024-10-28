<?php
date_default_timezone_set('America/Lima');
// Configuración de conexión a la base de datos
$dsn = "mysql:host=localhost;dbname=login_system;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, "root", "", $options);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Verificar si los datos han sido enviados correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['duracion'])) {
    $worker_id = $_POST['id'];
    $duracion = $_POST['duracion'];
    $hora_receso = date('Y-m-d H:i:s'); // Hora actual en formato de base de datos

    // Actualizar la tabla `trabajadores` con la `hora_receso` y la `duracion`
    $stmt = $conn->prepare("UPDATE trabajadores SET hora_receso = :hora_receso, duracion = :duracion, hora_vuelta = NULL WHERE id = :id");
    $stmt->execute([
        ':hora_receso' => $hora_receso,
        ':duracion' => $duracion,
        ':id' => $worker_id
    ]);

    // Verificar si se actualizó algún registro
    if ($stmt->rowCount()) {
        // Enviar respuesta JSON con éxito y la hora de inicio del receso
        echo json_encode(['status' => 'success', 'hora_receso' => $hora_receso, 'duracion' => $duracion]);
    } else {
        // Enviar respuesta JSON con error si no se actualizó ningún registro
        echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar el receso.']);
    }
} else {
    // Error si faltan datos
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
}
?>
