<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar todos los registros de la tabla visitas
$sql = "SELECT * FROM visitas";
$result = $conn->query($sql);

// Cabeceras para forzar la descarga del archivo Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=visitas_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Crear tabla de Excel
echo "<table border='1'>";
echo "<tr>
        <th>Nro.</th>
        <th>Fecha de visita</th>
        <th>Visitante</th>
        <th>Documento del visitante</th>
        <th>Hora Ingreso</th>
        <th>Hora Salida</th>
        <th>Motivo</th>
        <th>Lugar Específico</th>
      </tr>";

if ($result->num_rows > 0) {
    $nro = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $nro++ . "</td>";
        echo "<td>" . $row['fecha'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['dni'] . "</td>";
        echo "<td>" . $row['hora_ingreso'] . "</td>";
        echo "<td>" . $row['hora_salida'] . "</td>";
        echo "<td>" . $row['smotivo'] . "</td>";
        echo "<td>" . $row['lugar'] . "</td>";
        echo "</tr>";
    }
}
echo "</table>";

$conn->close();
?>
