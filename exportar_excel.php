<?php
// Incluir la biblioteca de PhpSpreadsheet
require 'vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado

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

// Consultar todos los registros de la tabla visitas
$sql = "SELECT * FROM visitas";
$result = $conn->query($sql);

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Escribir los encabezados de la tabla
$sheet->setCellValue('A1', 'Nro.')
      ->setCellValue('B1', 'Fecha de visita')
      ->setCellValue('C1', 'Visitante')
      ->setCellValue('D1', 'Documento del visitante')
      ->setCellValue('E1', 'Hora Ingreso')
      ->setCellValue('F1', 'Hora Salida')
      ->setCellValue('G1', 'Motivo')
      ->setCellValue('H1', 'Lugar Específico');

// Si hay resultados, escribir cada fila
if ($result->num_rows > 0) {
    $nro = 1;
    $rowIndex = 2; // Empezar a escribir en la fila 2
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $nro++)
              ->setCellValue('B' . $rowIndex, $row['fecha'])
              ->setCellValue('C' . $rowIndex, $row['nombre'])
              ->setCellValue('D' . $rowIndex, $row['dni'])
              ->setCellValue('E' . $rowIndex, $row['hora_ingreso'])
              ->setCellValue('F' . $rowIndex, $row['hora_salida'])
              ->setCellValue('G' . $rowIndex, $row['smotivo'])
              ->setCellValue('H' . $rowIndex, $row['lugar']);
        $rowIndex++;
    }
}

// Establecer los encabezados para la descarga de Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="visitas_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel y enviarlo al navegador para la descarga
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Cerrar la conexión
$conn->close();
?>
