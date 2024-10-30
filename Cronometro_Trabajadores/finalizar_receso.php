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

    // Obtener la hora de receso y la duración programada del trabajador
    $stmt = $conn->prepare("SELECT hora_receso, duracion FROM recesos WHERE trabajador_id = :id AND estado = 'activo'");
    $stmt->execute([':id' => $worker_id]);
    $receso = $stmt->fetch();

    if ($receso) {
        // Calcular la duración real del receso en minutos
        $horaReceso = new DateTime($receso['hora_receso']);
        $horaVuelta = new DateTime($hora_vuelta);
        $interval = $horaReceso->diff($horaVuelta);
        $duracionUsada = ($interval->h * 60) + $interval->i + ($interval->s > 0 ? 1 : 0); // Redondear hacia arriba si hay segundos

        // Calcular el exceso si la duración usada supera la duración programada
        $duracionProgramada = (int)$receso['duracion'];
        $exceso = max(0, $duracionUsada - $duracionProgramada);

        // Actualizar la tabla recesos con hora de vuelta, duración real y exceso
        $stmtUpdate = $conn->prepare("UPDATE recesos SET hora_vuelta = :hora_vuelta, duracion = :duracion_usada, exceso = :exceso, estado = 'finalizado' WHERE trabajador_id = :id AND estado = 'activo'");
        $stmtUpdate->execute([
            ':hora_vuelta' => $hora_vuelta,
            ':duracion_usada' => $duracionUsada,
            ':exceso' => $exceso,
            ':id' => $worker_id
        ]);

        // Actualizar la tabla trabajadores para establecer la hora de vuelta
        $stmtUpdateTrabajador = $conn->prepare("UPDATE trabajadores SET hora_vuelta = :hora_vuelta WHERE id = :id");
        $stmtUpdateTrabajador->execute([
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
