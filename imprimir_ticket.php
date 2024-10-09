<?php
// Añadir función SetDash() a FPDF
class PDF_Dash extends FPDF
{
    function SetDash($black = false, $white = false)
    {
        if ($black and $white) {
            $s = sprintf('[%.3F %.3F] 0 d', $black, $white);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }
}

// Cambiamos a usar la clase PDF_Dash
require 'vendor/autoload.php';  // Si usas Composer
use Fpdf\Fpdf;
include 'db.php';  // Conexión a la base de datos

$id = $_GET['id'];
$sql = "SELECT * FROM visitas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    // Crear el PDF con la clase extendida
    $pdf = new PDF_Dash('P', 'mm', array(80, 150));  // Ticket de tamaño pequeño
    $pdf->AddPage();
    
    // Fuente: Arial, Negrita, Tamaño 12
    $pdf->SetFont('Arial', 'B', 12);
    
    // Encabezado centrado
    $pdf->Cell(60, 10, "DIRECCION REGIONAL DE EDUCACION", 0, 1, 'C');

    // Línea de separación
    $pdf->Line(10, 20, 70, 20);  // Linea simple horizontal

    // Fuente: Arial, Regular, Tamaño 10
    $pdf->SetFont('Arial', '', 10);
    
    // Fecha a la izquierda y hora a la derecha
    $pdf->Cell(30, 6, "Fecha: " . $row['fecha'], 0, 0, 'L');  // A la izquierda
    $pdf->Cell(30, 6, "Hora: " . $row['hora_ingreso'], 0, 1, 'R');  // A la derecha

    // Línea punteada de separación
    $pdf->SetDash(1, 1);  // Activar modo punteado
    $pdf->Line(10, 30, 70, 30);  // Línea punteada horizontal
    $pdf->SetDash();  // Desactivar modo punteado

    // Texto centrado BIENVENIDO
    $pdf->Cell(60, 10, "BIENVENIDO", 0, 1, 'C');

    // Datos del visitante
    $pdf->Cell(60, 6, "Nombre: " . $row['nombre'], 0, 1, 'C');
    $pdf->Cell(60, 6, "DNI: " . $row['dni'], 0, 1, 'C');
    $pdf->Cell(60, 6, "Motivo: " . $row['smotivo'], 0, 1, 'C');
    $pdf->Cell(60, 6, "Numero: " . $row['id'], 0, 1, 'C');  // Usando 'id' como número de visita
    $pdf->Cell(60, 6, "Lugar: " . $row['lugar'], 0, 1, 'C');

    // Línea punteada de separación
    $pdf->SetDash(1, 1);
    $pdf->Line(10, 80, 70, 80);  // Línea punteada horizontal
    $pdf->SetDash();

    // Mensaje de agradecimiento
    $pdf->Cell(60, 10, "GRACIAS POR SU VISITA", 0, 1, 'C');

    // Mensaje en quechua
    $pdf->Cell(60, 10, "DIOSPAGRAUQUI", 0, 1, 'C');  // Ejemplo simple en quechua, se puede ajustar

    // Línea punteada de separación
    $pdf->SetDash(1, 1);
    $pdf->Line(10, 100, 70, 100);  // Línea punteada horizontal
    $pdf->SetDash();

    // Pie de página
    $pdf->Cell(60, 10, "www.drehuanuco.gob.pe", 0, 1, 'C');

    // Generar el PDF
    $pdf->Output();
}

$stmt->close();
$conn->close();
?>
