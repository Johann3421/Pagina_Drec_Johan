<?php
// Cargar la clase FPDF
require 'fpdf/fpdf.php';

class PDF_Dash extends FPDF
{
    function SetDash($black = false, $white = false)
    {
        if ($black && $white) {
            $s = sprintf('[%.3F %.3F] 0 d', $black, $white);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }
}

// Incluir el archivo de la base de datos
include 'db.php';  // Verifica que este archivo establezca la conexión correctamente

// Obtener el ID desde la URL
$id = $_GET['id'] ?? null;

if ($id) {
    // Preparar la consulta SQL
    $sql = "SELECT * FROM visitas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Crear el PDF usando la clase extendida PDF_Dash con un tamaño de ticket 90x125 mm
        $pdf = new PDF_Dash('P', 'mm', array(90, 125));
        $pdf->AddPage();

        // Agregar una imagen al encabezado (ajustada al tamaño del ticket)
        $pdf->Image('imagenes/logo_dre.png', 14, -15, 65, 55); // (ruta, x, y, ancho, alto)

        // Fuente: Arial, Negrita, Tamaño 10
        $pdf->SetFont('Arial', 'B', 12);

        // Encabezado centrado debajo de la imagen
        $pdf->Ln(15);  // Deja espacio debajo de la imagen
        $pdf->Cell(0, 10, "DIRECCION REGIONAL DE EDUCACION", 0, 1, 'C');

        // Línea de separación
        $pdf->Line(10, 35, 80, 35);  // Línea simple horizontal

        // Fuente: Arial, Regular, Tamaño 9 (más pequeño para que encaje en el ticket)
        $pdf->SetFont('Arial', '', 9);

        // Fecha a la izquierda y hora a la derecha
        $pdf->Ln(0);  // Deja un poco de espacio
        $pdf->Cell(30, 6, "Fecha: " . $row['fecha'], 0, 0, 'L');
        $pdf->Cell(40, 6, "Hora: " . $row['hora_ingreso'], 0, 1, 'R');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);  // Activar modo punteado
        $pdf->Line(10, 40, 80, 40);  // Línea punteada horizontal
        $pdf->SetDash();  // Desactivar modo punteado

        // Texto centrado BIENVENIDO
        $pdf->Ln(0);  // Espacio adicional
        $pdf->Cell(0, 10, "BIENVENIDO", 0, 1, 'C');

        // Datos del visitante
        $pdf->Ln(0);  // Espacio adicional
        $pdf->Cell(0, 6, $row['nombre'], 0, 1, 'C');
        $pdf->Cell(0, 6, "DNI: " . $row['dni'], 0, 1, 'C');
        $pdf->Cell(0, 6, "Motivo: " . $row['smotivo'], 0, 1, 'C');
        $pdf->Cell(0, 6, $row['lugar'], 0, 1, 'C');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);
        $pdf->Line(10, 78, 80, 78);  // Línea punteada horizontal
        $pdf->SetDash();

        // Mensaje de agradecimiento
        $pdf->Ln(0);  // Espacio antes del mensaje
        $pdf->Cell(0, 10, "GRACIAS POR SU VISITA", 0, 1, 'C');

        // Mensaje en quechua
        $pdf->Ln(-2);  // Espacio adicional
        $pdf->Cell(0, 6, "SHAMUSHQAYKITA PAKILLA", 0, 1, 'C');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);
        $pdf->Line(10, 88, 80, 88);  // Línea punteada horizontal
        $pdf->SetDash();

        // Pie de página
        $pdf->Ln(-2.5);  // Espacio adicional
        $pdf->Cell(0, 10, "www.drehuanuco.gob.pe", 0, 1, 'C');

        // Generar el PDF
        $pdf->Output();
    }

    $stmt->close();
}
$conn->close();
?>
