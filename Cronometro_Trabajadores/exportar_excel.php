<?php
// Incluir la biblioteca de PhpSpreadsheet
require '/xampp/htdocs/Proyecto_Johann/vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// Consultar todos los registros de la tabla recesos
$sql = "SELECT id, nombre, dni, hora_receso, hora_vuelta, duracion, exceso FROM recesos";
$result = $conn->query($sql);

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Escribir los encabezados de la tabla
$sheet->setCellValue('A1', 'Nro.')
      ->setCellValue('B1', 'Nombre')
      ->setCellValue('C1', 'DNI')
      ->setCellValue('D1', 'Hora de Inicio de Receso')
      ->setCellValue('E1', 'Hora de Fin de Receso')
      ->setCellValue('F1', 'Duración (minutos)')
      ->setCellValue('G1', 'Exceso (minutos)');

// Si hay resultados, escribir cada fila
if ($result->num_rows > 0) {
    $nro = 1;
    $rowIndex = 2; // Empezar a escribir en la fila 2
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $nro++)
              ->setCellValue('B' . $rowIndex, $row['nombre'])
              ->setCellValue('C' . $rowIndex, $row['dni'])
              ->setCellValue('D' . $rowIndex, $row['hora_receso'])
              ->setCellValue('E' . $rowIndex, $row['hora_vuelta'])
              ->setCellValue('F' . $rowIndex, $row['duracion'])
              ->setCellValue('G' . $rowIndex, $row['exceso'] ?? '0');
        $rowIndex++;
    }
}

// Establecer los encabezados para la descarga de Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="recesos_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel y enviarlo al navegador para la descarga
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Cerrar la conexión
$conn->close();
?>
