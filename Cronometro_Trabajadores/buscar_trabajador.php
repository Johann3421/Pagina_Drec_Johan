<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la búsqueda desde el frontend
$busqueda = $_GET['busqueda'] ?? '';

if (!empty($busqueda)) {
    // Realizar la consulta para buscar trabajadores por nombre o DNI
    $sql = "SELECT id, nombre, dni FROM trabajadores WHERE nombre LIKE ? OR dni LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $likeBusqueda = '%' . $busqueda . '%';
    $stmt->bind_param('ss', $likeBusqueda, $likeBusqueda);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mostrar los resultados en la búsqueda
        while ($row = $result->fetch_assoc()) {
            echo "<div onclick='seleccionarTrabajador(" . $row['id'] . ", \"" . $row['nombre'] . "\", \"" . $row['dni'] . "\")'>";
            echo $row['nombre'] . " (" . $row['dni'] . ")";
            echo "</div>";
        }
    } else {
        echo "No se encontraron resultados.";
    }
}
?>
