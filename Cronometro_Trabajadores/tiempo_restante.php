<?php
// tiempo_restante.php
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

    // Consultar la hora de receso y la duración
    $stmt = $conn->prepare("SELECT hora_receso, duracion FROM trabajadores WHERE id = :id AND hora_vuelta IS NULL");
    $stmt->execute([':id' => $worker_id]);
    $trabajador = $stmt->fetch();

    if ($trabajador) {
        $horaReceso = new DateTime($trabajador['hora_receso']);
        $duracionMinutos = (int)$trabajador['duracion'];
        $finReceso = clone $horaReceso;
        $finReceso->modify("+$duracionMinutos minutes");

        $ahora = new DateTime();
        $intervalo = $finReceso->getTimestamp() - $ahora->getTimestamp(); // En segundos

        if ($intervalo > 0) {
            echo json_encode(['status' => 'success', 'tiempo_restante' => $intervalo]);
        } else {
            echo json_encode(['status' => 'expired']); // Receso expirado
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay receso activo para este trabajador.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID del trabajador no proporcionado.']);
}
?>
