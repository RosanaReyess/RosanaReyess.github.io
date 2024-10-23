<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAE 2025</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Fuentes y estilos adicionales -->
    <style>
    body,
    header {
        font-family: 'Arial', sans-serif;
    }

    header {
        background-color: #e9f7ef;
        /* Fondo verde claro */
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    header h1 {
        color: #1d8348;
        /* Verde oscuro */
        font-size: 28px;
        margin: 0;
        font-weight: bold;
    }

    .navbar {
        margin-top: 15px;
    }
    </style>
</head>

<body>
    <header>
        <h1>Proyecto PAE 2025</h1>
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <nav class="navbar">
            <?php 
                // Obtener la página actual
                $current_page = basename($_SERVER['PHP_SELF']);

                // Verificar en qué página se encuentra para mostrar los botones correspondientes
                if ($current_page === 'index.php') { 
                    // Si estamos en la página principal, mostrar "Cerrar Sesión"
            ?>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            <?php 
                } else { 
                    // Si estamos en cualquier otra página, mostrar los botones de navegación
            ?>
            <a href="index.php" class="nav-btn">Volver al Inicio</a>
            <?php } ?>
        </nav>
        <?php endif; ?>
    </header>
    <main>