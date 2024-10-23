<?php
// index.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}
?>

<div class="card-container">
    <div class="card">
        <h3>Registrar Vacante</h3>
        <p>Haz clic para registrar una nueva vacante.</p>
        <a href="registrar_vacante.php" class="nav-btn">Registrar</a>
    </div>
    <div class="card">
        <h3>Ver Vacantes</h3>
        <p>Consulta las vacantes disponibles.</p>
        <a href="ver_vacantes.php" class="nav-btn">Ver</a>
    </div>
    <div class="card">
        <h3>Buscar Aspirante</h3>
        <p>Busca información sobre aspirantes.</p>
        <a href="buscar.php" class="nav-btn">Buscar</a>
    </div>
    <div class="card">
        <h3>Descargar Documentos</h3>
        <p>Accede a los documentos de los aspirantes.</p>
        <a href="descargas.php" class="nav-btn">Descargar</a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>