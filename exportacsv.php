<?php
// Iniciar sesión
session_start();

// Verificar si hay resultados en la sesión
if (!isset($_SESSION['resultados']) || empty($_SESSION['resultados'])) {
    die("No hay resultados para exportar.");
}

// Obtener los datos desde la sesión
$resultados = $_SESSION['resultados'];

// Establecer cabeceras para la descarga de CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="resultados_busqueda.csv"');

// Abrir la salida para escribir el CSV
$output = fopen('php://output', 'w');

// Definir las cabeceras de las columnas
fputcsv($output, ['Número de Identificación', 'Nombre', 'Años de Experiencia', 'Aptitud', 'Vacante ID', 'Fecha de Registro', 'Ubicación']);

// Escribir los datos de la búsqueda en el archivo CSV
foreach ($resultados as $row) {
    fputcsv($output, [
        $row['numero_identificacion'],
        $row['nombre'],
        $row['años_experiencia'],
        $row['aptitud'],
        $row['vacante_id'],
        $row['fecha_registro'],
        $row['ubicacion']
    ]);
}

// Cerrar el archivo CSV
fclose($output);
exit;
?>
