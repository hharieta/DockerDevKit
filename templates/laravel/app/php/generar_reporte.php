<?php
// Iniciar captura de salida
ob_start();

require('../fpdf/fpdf.php');
require_once './main.php'; // Asegúrate de incluir la conexión a la base de datos

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('../img/producto/logo.png',10,8,33); // Ajusta la ruta a tu logo
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30,10,'Reporte de Servicios Terminados',0,0,'C');
        // Salto de línea
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Crear nuevo PDF
$pdf = new PDF();
$pdf->AliasNbPages();

// Ajustar márgenes
$pdf->SetMargins(15, 20, 15);

// Registrar las fuentes
$pdf->AddFont('Arial', '', 'arial.php');
$pdf->AddFont('Arial', 'B', 'arialb.php');
$pdf->AddFont('Arial', 'I', 'ariali.php');
$pdf->AddFont('Arial', 'BI', 'arialbi.php');

$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Conexión a la base de datos y consulta de los servicios terminados
$conexion = conexion();
$consulta = "SELECT * FROM servicio WHERE estado='Terminado'";
$resultado = $conexion->query($consulta);
$servicios = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Agregar datos al PDF
foreach($servicios as $servicio) {
    // Ajustar margen izquierdo para el texto
    $pdf->Cell(0,10,'ID: ' . $servicio['servicio_id'] . ' - Producto ID: ' . $servicio['producto_id'] . ' - Tipo: ' . $servicio['tipo_servicio'] . ' - Observaciones: ' . $servicio['observaciones'],0,1,'L', false);
}

// Limpiar el buffer de salida antes de generar el PDF
ob_end_clean();

// Nombre del archivo con timestamp
$filename = 'reporte_' . time() . '.pdf';

// Salida del PDF
$pdf->Output('D', $filename);

?>
