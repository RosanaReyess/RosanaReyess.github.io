<?php
// Iniciar sesión
session_start();

// Verificar si hay resultados en la sesión
if (!isset($_SESSION['resultados']) || empty($_SESSION['resultados'])) {
    die("No hay resultados para exportar.");
}

// Importar la librería
require 'vendor/vedor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

try {
    // Crear nuevo archivo de Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Definir las cabeceras de las columnas
    $sheet->setCellValue('A1', 'Número de Identificación');
    $sheet->setCellValue('B1', 'Nombre');
    $sheet->setCellValue('C1', 'Años de Experiencia');
    $sheet->setCellValue('D1', 'Aptitud');
    $sheet->setCellValue('E1', 'Vacante ID');
    $sheet->setCellValue('F1', 'Fecha de Registro');
    $sheet->setCellValue('G1', 'Ubicación');

    // Obtener los datos desde la sesión
    $resultados = $_SESSION['resultados'];
    $row = 2; // Comenzar en la segunda fila (la primera tiene los encabezados)

    // Rellenar las filas con los datos
    foreach ($resultados as $data) {
        $sheet->setCellValue('A' . $row, $data['numero_identificacion']);
        $sheet->setCellValue('B' . $row, $data['nombre']);
        $sheet->setCellValue('C' . $row, $data['años_experiencia']);
        $sheet->setCellValue('D' . $row, $data['aptitud']);
        $sheet->setCellValue('E' . $row, $data['vacante_id']);
        $sheet->setCellValue('F' . $row, $data['fecha_registro']);
        $sheet->setCellValue('G' . $row, $data['ubicacion']);
        $row++;
    }

    // Limpiar cualquier salida anterior
    if (ob_get_contents()) {
        ob_end_clean(); // Limpiar el buffer
    }

    // Depuración: confirmar que los datos se han cargado correctamente
    // echo json_encode($resultados);
    // exit;

    // Establecer las cabeceras HTTP para la descarga de Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="resultados_busqueda.xlsx"');
    header('Cache-Control: max-age=0');

    // Guardar y enviar el archivo Excel al navegador
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    // Si hay un error, mostrar el mensaje
    echo "Error generando el archivo: " . $e->getMessage();
    exit;
}
?>
