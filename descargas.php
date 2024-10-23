<?php
// descargas.php
require_once 'includes/header.php';
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}

// Consulta para contar aspirantes habilitados y ponderados
$sql_habilitados = "SELECT COUNT(*) as total FROM aspirantes WHERE ubicacion = 'habilitado'";
$result_habilitados = $conn->query($sql_habilitados);
$row_habilitados = $result_habilitados->fetch_assoc();
$total_habilitados = $row_habilitados['total'];

$sql_ponderados = "SELECT COUNT(*) as total FROM aspirantes WHERE ubicacion = 'ponderado'";
$result_ponderados = $conn->query($sql_ponderados);
$row_ponderados = $result_ponderados->fetch_assoc();
$total_ponderados = $row_ponderados['total'];
?>

<div class="container"
    style="max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <h2 style="text-align: center; color: green;">Descargas y Listados</h2>
    <div class="card-container" style="display: flex; justify-content: space-around; margin: 20px 0;">
        <div class="card" onclick="location.href='descargas_ponderados.php'"
            style="flex: 1; margin: 0 10px; padding: 20px; background-color: #ffff; border-radius: 8px; cursor: pointer;">
            <h3>Aspirantes Ponderados</h3>
            <p>Descargar documentos de aspirantes ponderados.</p>
        </div>
        <div class="card" onclick="location.href='descargas_habilitados.php'"
            style="flex: 1; margin: 0 10px; padding: 20px; background-color: #ffff; border-radius: 8px; cursor: pointer;">
            <h3>Aspirantes Habilitados</h3>
            <p>Descargar documentos de aspirantes habilitados.</p>
        </div>
    </div>

</div>

<?php
require_once 'includes/footer.php';
?>