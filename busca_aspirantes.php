<?php
// busca_aspirantes.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$results = [];
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $buscar = trim($_POST['buscar']);
    $tipo = $_POST['tipo'];

    // Validar que se haya ingresado un valor de búsqueda
    if (empty($buscar)) {
        $error_message = "Por favor, ingrese un valor para buscar.";
    } else {
        if ($tipo == 'nombre') {
            $stmt = $conn->prepare("SELECT a.nombre, a.numero_identificacion, v.titulo, a.ubicacion 
                                    FROM aspirantes a 
                                    JOIN vacantes v ON a.vacante_id = v.id 
                                    WHERE a.nombre LIKE ? 
                                    ORDER BY a.nombre ASC");
            $like_buscar = "%" . $buscar . "%";
            $stmt->bind_param("s", $like_buscar);
        } else {
            $stmt = $conn->prepare("SELECT a.nombre, a.numero_identificacion, v.titulo, a.ubicacion 
                                    FROM aspirantes a 
                                    JOIN vacantes v ON a.vacante_id = v.id 
                                    WHERE a.numero_identificacion = ? 
                                    ORDER BY a.nombre ASC");
            $stmt->bind_param("s", $buscar);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            $error_message = "Error al ejecutar la consulta: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Aspirantes</title>
    <style>
    /* Agregar tus estilos CSS aquí */
    </style>
</head>

<body>
    <div class="main-container">
        <div class="search-container">
            <h2>Buscar Información de Aspirantes</h2>
            <form method="post" action="busca_aspirantes.php">
                <label for="tipo">Buscar por:</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Seleccione...</option>
                    <option value="nombre">Nombre</option>
                    <option value="numero_identificacion">Número de Identificación</option>
                </select>

                <label for="buscar">Valor:</label>
                <input type="text" name="buscar" id="buscar" required>

                <button type="submit">Buscar</button>
            </form>
            <?php if ($error_message) : ?>
            <p class="result-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>

        <div class="list-container">
            <h3>Listado de Aspirantes:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Número de Identificación</th>
                        <th>Nombre</th>
                        <th>Vacante</th>
                        <th>Aptitud</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)) : ?>
                    <tr>
                        <td colspan="4">No se encontraron resultados.</td>
                    </tr>
                    <?php else : ?>
                    <?php foreach ($results as $resultado) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resultado['numero_identificacion']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['ubicacion']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>