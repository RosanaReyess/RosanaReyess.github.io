<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$results = [];
$error_message = "";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $buscar = trim($_POST['buscar']);
    $tipo = $_POST['tipo'];

    // Validar el input
    if (empty($buscar) || empty($tipo)) {
        $error_message = "Por favor, complete todos los campos.";
    } else {
        // Seleccionar la consulta basada en el tipo de búsqueda
        if ($tipo == "nombre") {
            $stmt = $conn->prepare("SELECT * FROM aspirantes WHERE nombre LIKE ?");
            $buscar = "%$buscar%";  // Usar comodines para buscar por nombre
        } elseif ($tipo == "numero_identificacion") {
            $stmt = $conn->prepare("SELECT * FROM aspirantes WHERE numero_identificacion = ?");
        }

        $stmt->bind_param("s", $buscar);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                // Verificar si el aspirante está en habilitados o ponderados y obtener su vacante
                $hoja_vida = null;
                $vacante_id = null;

                if ($row['apto'] === 'habilitado') {
                    $stmt_habilitado = $conn->prepare("SELECT archivo_documento, vacante FROM descargas_habilitados WHERE numero_identificacion = ?");
                    $stmt_habilitado->bind_param("s", $row['numero_identificacion']);
                    $stmt_habilitado->execute();
                    $result_habilitado = $stmt_habilitado->get_result();
                    if ($file_row = $result_habilitado->fetch_assoc()) {
                        $hoja_vida = $file_row['archivo_documento'];
                        $vacante_id = $file_row['vacante'];  // Obtener vacante desde la tabla de habilitados
                    }
                } elseif ($row['apto'] === 'ponderado') {
                    $stmt_ponderado = $conn->prepare("SELECT archivo_documento, vacante FROM descargas_ponderados WHERE numero_identificacion = ?");
                    $stmt_ponderado->bind_param("s", $row['numero_identificacion']);
                    $stmt_ponderado->execute();
                    $result_ponderado = $stmt_ponderado->get_result();
                    if ($file_row = $result_ponderado->fetch_assoc()) {
                        $hoja_vida = $file_row['archivo_documento'];
                        $vacante_id = $file_row['vacante'];  // Obtener vacante desde la tabla de ponderados
                    }
                }

                // Asignar los datos obtenidos al resultado
                $row['hoja_vida'] = $hoja_vida;
                $row['vacante_id'] = $vacante_id;  // Asignar vacante ID
                $results[] = $row;
            }

            if (empty($results)) {
                $error_message = "No se encontraron resultados.";
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
        /* Estilos de la página */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #4CAF50;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .left-buttons a {
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            background-color: #333;
        }

        .main-container {
            display: flex;
            padding: 20px;
        }

        .search-container {
            flex: 1;
            padding: 20px;
        }

        .list-container {
            flex: 2;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="search-container">
            <h2>Buscar por Nombre o Número de Identificación</h2>
            <form method="post" action="buscar.php">
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
            <h3>Resultados de la Búsqueda</h3>
            <table>
                <thead>
                    <tr>
                        <th>Número de Identificación</th>
                        <th>Nombre</th>
                        <th>Años de Experiencia</th>
                        <th>Estado</th>
                        <th>Vacante</th> <!-- Mostrar vacante_id -->
                        <th>Fecha de Registro</th>
                        <th>Ubicación</th>
                        <th>Hoja de Vida</th> <!-- Nueva columna para la hoja de vida -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)) : ?>
                        <tr>
                            <td colspan="8" class="result-message">No se encontraron resultados.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['numero_identificacion']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['años_experiencia']); ?></td>
                                <td><?php echo htmlspecialchars($row['apto']); ?></td>
                                <td><?php echo htmlspecialchars($row['vacante_id']); ?></td> <!-- Mostrar vacante_id -->
                                <td><?php echo htmlspecialchars($row['fecha_registro']); ?></td>
                                <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                                <td>
                                    <?php if (!empty($row['hoja_vida'])) : ?>
                                        <a href="<?php echo htmlspecialchars($row['hoja_vida']); ?>" target="_blank">Ver Hoja de Vida</a>
                                    <?php else : ?>
                                        No disponible
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>





