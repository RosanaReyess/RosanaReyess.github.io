<?php
// ver_vacantes.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$query = "SELECT * FROM vacantes";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Vacantes</title>
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Vincular la hoja de estilos CSS -->
    <style>
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin: 10px;
        flex-basis: calc(30% - 20px);
        /* Ajustar el tamaño de la tarjeta */
        text-align: center;
    }

    .card h3 {
        margin: 0;
    }

    .nav-btn {
        display: inline-block;
        background-color: #28a745;
        /* Color verde */
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 10px;
        /* Espaciado entre botones */
    }

    .nav-btn:hover {
        background-color: #218838;
        /* Color verde más oscuro al pasar el mouse */
    }

    footer {
        background-color: #f5f5f5;
        border-top: 5px solid #ddd;
        position: relative;
        bottom: 0;
        width: 100%;
        margin-top: 30px;
        text-align: center;
        padding: 10px 0;
    }
    </style>
</head>

<body>

    <div class="card-container">
        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
            <p>Experiencia Requerida: <?php echo htmlspecialchars($row['total_meses_requeridos']); ?> meses</p>
            <a href="ver_inscritos.php?vacante_id=<?php echo $row['id']; ?>" class="nav-btn">Ver Aspirantes</a>
            <a href="registrar_aspirante.php?vacante_id=<?php echo $row['id']; ?>" class="nav-btn">Registrar
                Aspirante</a>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>Sin vacantes disponibles.</p>
        <?php endif; ?>
    </div>


    <?php
require_once 'includes/footer.php';
?>
</body>

</html>