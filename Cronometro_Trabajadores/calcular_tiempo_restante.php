<?php
// calcular_tiempo_restante.php
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

// Consultar todos los trabajadores en receso activo
$stmt = $conn->prepare("SELECT id, hora_receso, duracion FROM trabajadores WHERE hora_receso IS NOT NULL AND hora_vuelta IS NULL");
$stmt->execute();
$trabajadores = $stmt->fetchAll();

$resultado = [];

foreach ($trabajadores as $trabajador) {
    $horaReceso = new DateTime($trabajador['hora_receso']);
    $duracionMinutos = (int)$trabajador['duracion'];
    $finReceso = clone $horaReceso;
    $finReceso->modify("+$duracionMinutos minutes");

    $ahora = new DateTime();
    $intervalo = $finReceso->getTimestamp() - $ahora->getTimestamp(); // Tiempo en segundos

    if ($intervalo > 0) {
        $resultado[] = [
            'id' => $trabajador['id'],
            'tiempo_restante' => $intervalo
        ];
    } else {
        // Si el tiempo ya expiró, marcarlo como 0
        $resultado[] = [
            'id' => $trabajador['id'],
            'tiempo_restante' => 0
        ];
    }
}

echo json_encode(['status' => 'success', 'trabajadores' => $resultado]);
?>
