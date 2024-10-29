<?php
date_default_timezone_set('America/Lima');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $worker_id = $_POST['id'];
    $hora_vuelta = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("SELECT hora_receso FROM trabajadores WHERE id = :id AND hora_vuelta IS NULL");
    $stmt->execute([':id' => $worker_id]);
    $trabajador = $stmt->fetch();

    if ($trabajador) {
        $stmt = $conn->prepare("UPDATE trabajadores SET hora_vuelta = :hora_vuelta WHERE id = :id");
        $stmt->execute([
            ':hora_vuelta' => $hora_vuelta,
            ':id' => $worker_id
        ]);

        // Actualizar el receso en la tabla `recesos`
        $stmtReceso = $conn->prepare("UPDATE recesos SET hora_vuelta = :hora_vuelta, estado = 'finalizado' WHERE trabajador_id = :id AND estado = 'activo'");
        $stmtReceso->execute([
            ':hora_vuelta' => $hora_vuelta,
            ':id' => $worker_id
        ]);

        echo json_encode(['status' => 'success', 'hora_vuelta' => $hora_vuelta]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay receso activo para este trabajador.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
}
?>
