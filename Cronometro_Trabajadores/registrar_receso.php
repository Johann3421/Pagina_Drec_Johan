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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['duracion'])) {
    $worker_id = $_POST['id'];
    $duracion = (int)$_POST['duracion'];
    $hora_receso = date('Y-m-d H:i:s');

    // Actualizar el receso en la tabla `trabajadores`
    $stmt = $conn->prepare("UPDATE trabajadores SET hora_receso = :hora_receso, duracion = :duracion, hora_vuelta = NULL WHERE id = :id");
    $stmt->execute([
        ':hora_receso' => $hora_receso,
        ':duracion' => $duracion,
        ':id' => $worker_id
    ]);

    if ($stmt->rowCount() > 0) {
        // Recuperar nombre y DNI del trabajador
        $stmtTrabajador = $conn->prepare("SELECT nombre, dni FROM trabajadores WHERE id = :id");
        $stmtTrabajador->execute([':id' => $worker_id]);
        $trabajador = $stmtTrabajador->fetch(PDO::FETCH_ASSOC);

        if ($trabajador) {
            // Registrar el receso en la tabla `recesos` con nombre y dni
            $stmtReceso = $conn->prepare("
                INSERT INTO recesos (trabajador_id, nombre, dni, duracion, hora_receso, estado) 
                VALUES (:id, :nombre, :dni, :duracion, :hora_receso, 'activo')
            ");
            $stmtReceso->execute([
                ':id' => $worker_id,
                ':nombre' => $trabajador['nombre'],
                ':dni' => $trabajador['dni'],
                ':duracion' => $duracion,
                ':hora_receso' => $hora_receso
            ]);

            echo json_encode([
                'status' => 'success',
                'id' => $worker_id,
                'nombre' => $trabajador['nombre'],
                'dni' => $trabajador['dni'],
                'hora_receso' => $hora_receso,
                'duracion' => $duracion
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Trabajador no encontrado.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar el receso en la base de datos.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos en la solicitud.']);
}
?>
