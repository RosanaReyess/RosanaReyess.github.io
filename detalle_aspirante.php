<?php
// detalle_aspirante.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si se ha pasado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>ID de aspirante inválido.</p>";
    exit;
}

$aspirante_id = intval($_GET['id']);

// Preparar y ejecutar la consulta para obtener la información del aspirante
$stmt = $conn->prepare("SELECT a.nombre, a.numero_identificacion, v.titulo, a.ubicacion, a.correo, a.telefono 
                        FROM aspirantes a 
                        JOIN vacantes v ON a.vacante_id = v.id 
                        WHERE a.id = ?");
$stmt->bind_param("i", $aspirante_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Aspirante no encontrado.</p>";
    exit;
}

$aspirante = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Aspirante</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
    }

    .detalle-container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        box-sizing: border-box;
    }

    .detalle-container h2 {
        text-align: center;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .detalle-container p {
        font-size: 1em;
        margin: 10px 0;
    }

    .detalle-container a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .detalle-container a:hover {
        background-color: #45a049;
    }
    </style>
</head>

<body>
    <div class="detalle-container">
        <h2>Detalles del Aspirante</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($aspirante['nombre']); ?></p>
        <p><strong>Número de Identificación:</strong>
            <?php echo htmlspecialchars($aspirante['numero_identificacion']); ?></p>
        <p><strong>Vacante:</strong> <?php echo htmlspecialchars($aspirante['titulo']); ?></p>
        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($aspirante['ubicacion']); ?></p>
        <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($aspirante['correo']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($aspirante['telefono']); ?></p>

        <a href="buscar.php">Volver a Buscar</a>
    </div>
</body>

</html>
<?php
require_once 'includes/footer.php';
?>