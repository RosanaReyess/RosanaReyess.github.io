<?php
// Iniciar la sesión para transmitir mensajes
session_start();

// Configuración para mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once 'includes/config.php';

// Función para convertir checkbox a 'Sí' o 'No'
function checkboxToSiNo($value) {
    return isset($value) && $value ? 'Sí' : 'No';
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Acceso inválido.";
    $_SESSION['message_type'] = "error";
    header("Location: registrar_aspirante.php");
    exit;
}

// Recibir y sanitizar datos del formulario
$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tipo_identificacion = filter_input(INPUT_POST, 'tipo_identificacion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$numero_identificacion = filter_input(INPUT_POST, 'numero_identificacion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
$telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$vacante = filter_input(INPUT_POST, 'vacante', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$anos_experiencia = filter_input(INPUT_POST, 'anos_experiencia', FILTER_VALIDATE_INT);
$meses_experiencia = filter_input(INPUT_POST, 'meses_experiencia', FILTER_VALIDATE_INT);
$total_experiencia_meses = filter_input(INPUT_POST, 'total_experiencia_meses', FILTER_VALIDATE_INT);
$apto = filter_input(INPUT_POST, 'apto', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Guardamos el valor de apto como texto (habilitado o ponderado)
$ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Convertir checkboxes a 'Sí' o 'No'
$hoja_vida = checkboxToSiNo($_POST['verificar_hoja_vida'] ?? null);
$carta_intencion = checkboxToSiNo($_POST['verificar_carta_intencion'] ?? null);
$fotocopia_cedula = checkboxToSiNo($_POST['verificar_fotocopia_cedula'] ?? null);
$libreta_militar = checkboxToSiNo($_POST['verificar_libreta_militar'] ?? null);
$cert_residencia = checkboxToSiNo($_POST['verificar_cert_residencia'] ?? null);
$cert_examen = checkboxToSiNo($_POST['verificar_cert_examen'] ?? null);
$cert_RNMC = checkboxToSiNo($_POST['verificar_cert_RNMC'] ?? null);
$curso_higiene = checkboxToSiNo($_POST['verificar_curso_higiene'] ?? null);
$curso_proteccion = checkboxToSiNo($_POST['verificar_curso_proteccion'] ?? null);
$experiencia_PAE = checkboxToSiNo($_POST['verificar_experiencia_pae'] ?? null);

// Manejo del archivo (hoja de vida)
$archivo_ruta = '';
$directorio_destino = 'uploads/'; // Ruta donde se almacenarán los archivos
$archivo_hoja_vida = $_FILES['hoja_vida']['name'];
$archivo_temporal = $_FILES['hoja_vida']['tmp_name'];
$archivo_tipo = $_FILES['hoja_vida']['type'];

// Verificar si el archivo fue cargado
if (!empty($archivo_hoja_vida)) {
    $archivo_extension = pathinfo($archivo_hoja_vida, PATHINFO_EXTENSION);
    $nombre_archivo_nuevo = $numero_identificacion . '_hoja_vida.' . $archivo_extension; // Generar un nombre único para el archivo
    $archivo_ruta = $directorio_destino . $nombre_archivo_nuevo;

    // Mover el archivo al directorio de destino
    if (move_uploaded_file($archivo_temporal, $archivo_ruta)) {
        $_SESSION['message'] = "Archivo subido correctamente.";
    } else {
        $_SESSION['message'] = "Error al subir el archivo.";
        $_SESSION['message_type'] = "error";
        header("Location: registrar_aspirante.php");
        exit;
    }
}

// Verificar que los campos esenciales estén presentes y no vacíos
$required_fields = [
    'nombre' => $nombre,
    'tipo_identificacion' => $tipo_identificacion,
    'numero_identificacion' => $numero_identificacion,
    'correo' => $correo,
    'telefono' => $telefono,
    'vacante' => $vacante,
    'anos_experiencia' => $anos_experiencia,
    'meses_experiencia' => $meses_experiencia,
    'apto' => $apto, // Aseguramos que apto es obligatorio y se guarda como texto
    'ubicacion' => $ubicacion // El campo de municipio ahora es obligatorio
];

foreach ($required_fields as $field => $value) {
    if (empty($value) && $value !== 0) { // '0' es válido para números
        $_SESSION['message'] = "El campo '$field' es obligatorio.";
        $_SESSION['message_type'] = "error";
        header("Location: registrar_aspirante.php");
        exit;
    }
}

// Calcular la experiencia total en meses si no se recibió correctamente
if (empty($total_experiencia_meses)) {
    $total_experiencia_meses = ($anos_experiencia * 12) + ($meses_experiencia ?? 0);
}

// Capturar la fecha actual para el registro
$fecha_registro = date('Y-m-d H:i:s');

// Validar si la cédula ya está registrada
$checkSql = $conn->prepare("SELECT * FROM aspirantes WHERE numero_identificacion = ?");
if ($checkSql === false) {
    $_SESSION['message'] = "Error en la preparación de la consulta: " . $conn->error;
    $_SESSION['message_type'] = "error";
    header("Location: registrar_aspirante.php");
    exit;
}

$checkSql->bind_param("s", $numero_identificacion);
$checkSql->execute();
$checkResult = $checkSql->get_result();

if ($checkResult->num_rows > 0) {
    $_SESSION['message'] = "La cédula ya está registrada.";
    $_SESSION['message_type'] = "error";
    header("Location: registrar_aspirante.php");
    exit;
} else {
    // Insertar en la tabla 'aspirantes'
    $insertSql = "INSERT INTO aspirantes (
                    nombre, tipo_identificacion, numero_identificacion, correo, telefono, vacante, 
                    `años_experiencia`, meses_experiencia, hoja_vida, carta_intencion, 
                    fotocopia_cedula, libreta_militar, cert_residencia, cert_examen, 
                    cert_RNMC, curso_higiene, curso_proteccion, experiencia_PAE, 
                    total_experiencia_meses, apto, ubicacion, fecha_registro, archivo_documento
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertSql);
    if ($stmt === false) {
        $_SESSION['message'] = "Error en la preparación de la consulta: " . $conn->error;
        $_SESSION['message_type'] = "error";
        header("Location: registrar_aspirante.php");
        exit;
    }

    $stmt->bind_param(
        "ssssssiiissssssssisssss", // Se incluye archivo_documento como string
        $nombre, 
        $tipo_identificacion, 
        $numero_identificacion, 
        $correo, 
        $telefono, 
        $vacante, 
        $anos_experiencia, 
        $meses_experiencia, 
        $hoja_vida, 
        $carta_intencion, 
        $fotocopia_cedula, 
        $libreta_militar, 
        $cert_residencia, 
        $cert_examen, 
        $cert_RNMC, 
        $curso_higiene, 
        $curso_proteccion, 
        $experiencia_PAE, 
        $total_experiencia_meses, 
        $apto, 
        $ubicacion, 
        $fecha_registro,
        $archivo_ruta // Añadir ruta del archivo aquí
    );

    if ($stmt->execute()) {
        // Dependiendo del estado 'apto', insertar en la tabla correspondiente
        if ($apto === 'habilitado') {
            $insertHabilitadosSql = "INSERT INTO descargas_habilitados (
                                        numero_identificacion, nombre, años_experiencia, verificar_hoja_vida, 
                                        vacante_id, fecha_registro, ubicacion, correo, telefono, 
                                        tipo_identificacion, vacante, meses_experiencia, archivo_documento
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtHabilitados = $conn->prepare($insertHabilitadosSql);
            $stmtHabilitados->bind_param(
                "sssssssssssss", 
                $numero_identificacion, 
                $nombre, 
                $anos_experiencia, 
                $hoja_vida, 
                $vacante, 
                $fecha_registro,  
                $ubicacion, 
                $correo, 
                $telefono, 
                $tipo_identificacion, 
                $vacante, 
                $meses_experiencia,
                $archivo_ruta // Guardar ruta en descargas_habilitados
            );
            $stmtHabilitados->execute();
            $stmtHabilitados->close();
        } elseif ($apto === 'ponderado') {
            $insertPonderadosSql = "INSERT INTO descargas_ponderados (
                                        numero_identificacion, nombre, años_experiencia, verificar_hoja_vida, 
                                        vacante_id, fecha_registro, ubicacion, correo, telefono, 
                                        tipo_identificacion, vacante, meses_experiencia, archivo_documento
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtPonderados = $conn->prepare($insertPonderadosSql);
            $stmtPonderados->bind_param(
                "sssssssssssss", 
                $numero_identificacion, 
                $nombre, 
                $anos_experiencia, 
                $hoja_vida, 
                $vacante, 
                $fecha_registro,  
                $ubicacion, 
                $correo, 
                $telefono, 
                $tipo_identificacion, 
                $vacante, 
                $meses_experiencia,
                $archivo_ruta // Guardar ruta en descargas_ponderados
            );
            $stmtPonderados->execute();
            $stmtPonderados->close();
        }
        $_SESSION['message'] = "Aspirante registrado con éxito.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error al guardar el aspirante: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
}

// Redirigir a la página principal
header("Location: registrar_aspirante.php");
exit;

// Cerrar conexión
$conn->close();





