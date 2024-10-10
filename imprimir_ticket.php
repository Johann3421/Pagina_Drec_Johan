<?php
// Cargar autoload de Composer y la clase FPDF
require 'fpdf/fpdf.php';  // Ruta correcta de fpdf.php

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
        // Crear el PDF usando la clase extendida PDF_Dash
        $pdf = new PDF_Dash('P', 'mm', array(90, 120));  // Ticket de tamaño pequeño
        $pdf->AddPage();

        // Fuente: Arial, Negrita, Tamaño 12
        $pdf->SetFont('Arial', 'B', 12);

        // Encabezado centrado
        $pdf->Cell(70, 10, "DIRECCION REGIONAL DE EDUCACION", 0, 1, 'C');

        // Línea de separación
        $pdf->Line(0, 20, 90, 20);  // Linea simple horizontal

        // Fuente: Arial, Regular, Tamaño 10
        $pdf->SetFont('Arial', '', 10);

        // Fecha a la izquierda y hora a la derecha
        $pdf->Cell(30, 6, "Fecha: " . $row['fecha'], 0, 0, 'L');
        $pdf->Cell(38, 6, "Hora: " . $row['hora_ingreso'], 0, 1, 'R');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);  // Activar modo punteado
        $pdf->Line(0, 26, 90, 26);  // Línea punteada horizontal
        $pdf->SetDash();  // Desactivar modo punteado

        // Texto centrado BIENVENIDO
        $pdf->Cell(70, 10, "BIENVENIDO", 0, 1, 'C');

        // Datos del visitante
        $pdf->Cell(70, 6, "Nombre: " . $row['nombre'], 0, 1, 'C');
        $pdf->Cell(70, 6, "DNI: " . $row['dni'], 0, 1, 'C');
        $pdf->Cell(70, 6, "Motivo: " . $row['smotivo'], 0, 1, 'C');
        $pdf->Cell(70, 6, "Numero: " . $row['id'], 0, 1, 'C');  // Usando 'id' como número de visita
        $pdf->Cell(70, 6, "Lugar: " . $row['lugar'], 0, 1, 'C');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);
        $pdf->Line(0, 67, 90, 67);  // Línea punteada horizontal
        $pdf->SetDash();

        // Mensaje de agradecimiento
        $pdf->Cell(70, 10, "GRACIAS POR SU VISITA", 0, 1, 'C');

        // Mensaje en quechua
        $pdf->Cell(70, 6, "DIOSPAGRAUQUI", 0, 1, 'C');

        // Línea punteada de separación
        $pdf->SetDash(1, 1);
        $pdf->Line(0, 83, 90, 83);  // Línea punteada horizontal
        $pdf->SetDash();

        // Pie de página
        $pdf->Cell(70, 10, "www.drehuanuco.gob.pe", 0, 1, 'C');

        // Generar el PDF
        $pdf->Output();
    }

    $stmt->close();
}
$conn->close();
?>
