<?php
// buscar_trabajador.php
include('db.php'); // Incluye tu archivo de conexión a la base de datos

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$sql = "SELECT * FROM trabajadores WHERE nombre LIKE ? OR dni LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$busqueda_param = '%' . $busqueda . '%';
$stmt->bind_param('ss', $busqueda_param, $busqueda_param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>{$row['nombre']} - DNI: {$row['dni']}</div>";
    }
} else {
    echo "No se encontraron trabajadores.";
}
?>
