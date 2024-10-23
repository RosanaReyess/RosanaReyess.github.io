<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Obtener el ID de la vacante
if (isset($_GET['vacante_id'])) {
    $vacante_id = $_GET['vacante_id'];

    // Consulta para obtener el nombre de la vacante y los meses requeridos de experiencia
    $vacante_query = "SELECT titulo, total_meses_requeridos FROM vacantes WHERE id = ?";
    $stmt = $conn->prepare($vacante_query);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $vacante_id);
    $stmt->execute();
    $stmt->bind_result($vacante_titulo, $meses_requeridos_vacante);
    $stmt->fetch();
    $stmt->close();

    // Consulta para obtener los aspirantes de la vacante seleccionada, incluyendo tipo de identificación
    $query = "SELECT tipo_identificacion, numero_identificacion, nombre, hoja_vida, carta_intencion, fotocopia_cedula, libreta_militar, 
                     cert_residencia, cert_examen, cert_RNMC, curso_higiene, curso_proteccion, experiencia_PAE, apto, archivo_documento, total_experiencia_meses
              FROM aspirantes WHERE vacante = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $vacante_titulo);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("location: ver_vacantes.php");
    exit;
}
?>

<!-- CSS personalizado para la tabla y el container -->
<style>
.container {
    width: 90%;
    margin: 20px auto;
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    color: #28a745;
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table,
th,
td {
    border: 1px solid #ddd;
}

th,
td {
    padding: 8px;
    text-align: center;
}

th {
    background-color: green;
    color: white;
}

td {
    background-color: #ffffff;
}

.no-data {
    color: #d9534f;
    font-weight: bold;
}

.download-link {
    color: #007bff;
    text-decoration: none;
}

.download-link:hover {
    text-decoration: underline;
}
</style>

<div class="container">
    <h2>Aspirantes para la Vacante: <?php echo htmlspecialchars($vacante_titulo); ?></h2>

    <!-- Mostrar mensajes de sesión -->
    <?php
    if (isset($_SESSION['message'])) {
        $message_type = $_SESSION['message_type'] == "success" ? "alert-success" : "alert-danger";
        echo "<div class='alert $message_type'>{$_SESSION['message']}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Tipo de Identificación</th> <!-- Nueva columna -->
                <th>Número de Identificación</th>
                <th>Nombres y Apellidos</th>
                <th>Hoja de Vida</th>
                <th>Carta de Intención</th>
                <th>Fotocopia de Cédula</th>
                <th>Libreta Militar</th>
                <th>Certificado de Residencia</th>
                <th>Examen de Salud</th>
                <th>Certificado RNMC</th>
                <th>Curso de Higiene</th>
                <th>Curso Protección</th>
                <th>Experiencia en PAE</th>
                <th>Apto</th> <!-- Columna para mostrar si es apto o no -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <!-- Rellenar la tabla con los datos de los aspirantes -->
            <?php while ($aspirante = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($aspirante['tipo_identificacion']); ?></td> <!-- Mostrar tipo de identificación -->
                <td><?php echo htmlspecialchars($aspirante['numero_identificacion']); ?></td>
                <td><?php echo htmlspecialchars($aspirante['nombre']); ?></td>
                <td>
                    <?php if (!empty($aspirante['archivo_documento'])): ?>
                    <a class="download-link"
                        href="<?php echo htmlspecialchars($aspirante['archivo_documento']); ?>" target="_blank">
                        Ver Hoja de Vida
                    </a>
                    <?php else: ?>
                    <span class="no-data">Sin cargar</span>
                    <?php endif; ?>
                </td>
                <td><?php echo ($aspirante['carta_intencion'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['fotocopia_cedula'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['libreta_militar'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['cert_residencia'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['cert_examen'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['cert_RNMC'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['curso_higiene'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['curso_proteccion'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td><?php echo ($aspirante['experiencia_PAE'] == 'Sí') ? 'Sí' : 'No'; ?></td>
                <td>
                    <?php
                    // Comparar experiencia del aspirante con la requerida por la vacante
                    if ($aspirante['total_experiencia_meses'] >= $meses_requeridos_vacante) {
                        echo 'Sí';
                    } else {
                        echo 'No';
                    }
                    ?>
                </td> <!-- Comparar experiencia para la columna Apto -->
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="14" class="text-center">No hay aspirantes registrados para esta vacante.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>


