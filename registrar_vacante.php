<?php
// registrar_vacante.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que las claves existen en $_POST
    $titulo = isset($_POST['nombre_vacante']) ? trim($_POST['nombre_vacante']) : '';
    $descripcion = isset($_POST['conocimientos']) ? trim($_POST['conocimientos']) : '';
    $anios_experiencia = isset($_POST['anios_experiencia']) ? intval($_POST['anios_experiencia']) : 0;
    $meses_experiencia = isset($_POST['meses_experiencia']) ? intval($_POST['meses_experiencia']) : 0;
    $total_meses_experiencia = ($anios_experiencia * 12) + $meses_experiencia; // Calcular el total de meses de experiencia

    $stmt = $conn->prepare("INSERT INTO vacantes (titulo, descripcion, años_experiencia_requeridos, meses_experiencia_requeridos, total_meses_requeridos) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $titulo, $descripcion, $anios_experiencia, $meses_experiencia, $total_meses_experiencia);

    if ($stmt->execute()) {
        $message = "Vacante registrada exitosamente.";
    } else {
        $message = "Error al registrar la vacante.";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vacante</title>
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Vincular la hoja de estilos CSS -->
    <script src="assets/js/calculo_experiencia_vacante.js"></script> <!-- Enlace al archivo JavaScript -->
</head>

<body>
    <?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" action="registrar_vacante.php">
        <h2>Registrar Nueva Vacante</h2>

        <label for="nombre_vacante">Nombre de la Vacante</label>
        <input type="text" name="nombre_vacante" id="nombre_vacante" required>

        <label for="conocimientos">Conocimientos Requeridos</label>
        <input type="text" name="conocimientos" id="conocimientos" required>

        <label class="experiencia-requerida">Experiencia Requerida</label>
        <div class="experiencia-container">
            <div class="experiencia-item">
                <label for="anios_experiencia">Años</label>
                <input type="number" name="anios_experiencia" id="anios_experiencia" required>
            </div>

            <div class="experiencia-item">
                <label for="meses_experiencia">Meses</label>
                <input type="number" name="meses_experiencia" id="meses_experiencia">
            </div>
        </div>

        <label for="pais">País</label>
        <select name="pais" id="pais">
            <option value="Colombia" selected>Colombia</option>
        </select>

        <label for="departamento">Departamento</label>
        <select name="departamento" id="departamento">
            <option value="Bolívar" selected>Bolívar</option>
        </select>
        <label for="municipio">Municipio</label>
        <select name="municipio" id="municipio">
            <option value="">Seleccione un municipio</option>
            <option value="Achí">Achí</option>
            <option value="Altos del Rosario">Altos del Rosario</option>
            <option value="Arenal">Arenal</option>
            <option value="Arjona">Arjona</option>
            <option value="Arroyohondo">Arroyohondo</option>
            <option value="Barranco de Loba">Barranco de Loba</option>
            <option value="Calamar">Calamar</option>
            <option value="Cantagallo">Cantagallo</option>
            <option value="Cartagena">Cartagena</option>
            <option value="Cicuco">Cicuco</option>
            <option value="Clemencia">Clemencia</option>
            <option value="Córdoba">Córdoba</option>
            <option value="El Carmen de Bolívar">El Carmen de Bolívar</option>
            <option value="El Guamo">El Guamo</option>
            <option value="El Peñón">El Peñón</option>
            <option value="Hatillo de Loba">Hatillo de Loba</option>
            <option value="Magangué">Magangué</option>
            <option value="Mahates">Mahates</option>
            <option value="Margarita">Margarita</option>
            <option value="María La Baja">María La Baja</option>
            <option value="Mompós">Mompós</option>
            <option value="Montecristo">Montecristo</option>
            <option value="Morales">Morales</option>
            <option value="Norosí">Norosí</option>
            <option value="Pinillos">Pinillos</option>
            <option value="Regidor">Regidor</option>
            <option value="Río Viejo">Río Viejo</option>
            <option value="San Cristóbal">San Cristóbal</option>
            <option value="San Estanislao">San Estanislao</option>
            <option value="San Fernando">San Fernando</option>
            <option value="San Jacinto">San Jacinto</option>
            <option value="San Jacinto del Cauca">San Jacinto del Cauca</option>
            <option value="San Juan Nepomuceno">San Juan Nepomuceno</option>
            <option value="San Martín de Loba">San Martín de Loba</option>
            <option value="San Pablo">San Pablo</option>
            <option value="Santa Catalina">Santa Catalina</option>
            <option value="Santa Rosa">Santa Rosa</option>
            <option value="Santa Rosa del Sur">Santa Rosa del Sur</option>
            <option value="Simití">Simití</option>
            <option value="Soplaviento">Soplaviento</option>
            <option value="Talaigua Nuevo">Talaigua Nuevo</option>
            <option value="Tiquisio">Tiquisio</option>
            <option value="Turbaco">Turbaco</option>
            <option value="Turbaná">Turbaná</option>
            <option value="Villanueva">Villanueva</option>
            <option value="Zambrano">Zambrano</option>
        </select>

        <button type="submit">Registrar Vacante</button>
    </form>

    <script>
    // Función para calcular el total de meses de experiencia de la vacante
    function calcularTotalMeses() {
        const anios = parseInt(document.getElementById('anios_experiencia').value) || 0;
        const meses = parseInt(document.getElementById('meses_experiencia').value) || 0;
        const totalMeses = (anios * 12) + meses;
        // Asegúrate de tener un campo para mostrar el total de meses si es necesario
        // document.getElementById('total_meses_experiencia').value = totalMeses;
    }

    // Añade eventos para calcular al cambiar los campos
    document.getElementById('anios_experiencia').addEventListener('input', calcularTotalMeses);
    document.getElementById('meses_experiencia').addEventListener('input', calcularTotalMeses);
    </script>
</body>

</html>

<?php
    require_once 'includes/footer.php';
    ?>