<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargas Ponderados</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    /* Estilo de la barra de navegación */
    .navbar {
        background-color: #4CAF50;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar .left-buttons {
        display: flex;
        gap: 10px;
    }

    .navbar .left-buttons a {
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        background-color: #333;
        border-radius: 5px;
    }

    .navbar h1 {
        color: white;
        margin: 0;
    }

    /* Estilo del contenedor */
    .container {
        width: 90%;
        margin: 20px auto;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    /* Estilo de la tabla */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px;
        text-align: center;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .no-records {
        text-align: center;
        font-size: 1.2em;
        color: #FF5733;
    }

    /* Ajustar tamaños de columnas */
    .id-column {
        width: 15%;
        /* Tamaño reducido para el número de identificación */
    }

    .name-column {
        width: 35%;
        /* Tamaño aumentado para el nombre */
    }
    </style>
</head>

<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <div class="left-buttons">
            <a href="javascript:history.back()">Regresar</a>
            <a href="index.php">Volver al Inicio</a>
        </div>
        <h1>Proyecto PAE 2025</h1>
    </div>

    <div class="container">
        <h2 style="text-align: center; color: #4CAF50;">Aspirantes Ponderados</h2>

        <table>
            <thead>
                <tr>
                    <th>Tipo de Identificación</th> <!-- Nueva columna para el tipo de identificación -->
                    <th class="id-column">Número de Identificación</th>
                    <th class="name-column">Nombre</th>
                    <th>Años de Experiencia</th>
                    <th>Cumple</th> <!-- Nueva columna para verificar si cumple -->
                    <th>Vacante</th>
                    <th>Fecha de Registro</th>
                    <th>Ubicación</th>
                    <th>Hoja de Vida</th> <!-- Nueva columna para Hoja de Vida -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Conexión a la base de datos
                require_once 'includes/config.php';

                // Obtener aspirantes de la tabla 'descargas_ponderados' y la experiencia requerida por la vacante
                $query = "SELECT p.numero_identificacion, p.nombre, p.tipo_identificacion, p.años_experiencia, p.verificar_hoja_vida, p.vacante, p.fecha_registro, p.ubicacion, p.archivo_documento, p.meses_experiencia, v.total_meses_requeridos
                          FROM descargas_ponderados p
                          JOIN vacantes v ON p.vacante = v.titulo";
                $result = $conn->query($query);

                // Verificar si hay registros
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Calcular la experiencia total en meses del aspirante
                        $total_experiencia = ($row['años_experiencia'] * 12) + $row['meses_experiencia'];

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['tipo_identificacion']) . '</td>'; // Mostrar el tipo de identificación
                        echo '<td>' . htmlspecialchars($row['numero_identificacion']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['años_experiencia']) . ' años, ' . htmlspecialchars($row['meses_experiencia']) . ' meses</td>';

                        // Mostrar si el aspirante cumple o no con los meses requeridos por la vacante
                        if ($total_experiencia >= $row['total_meses_requeridos']) {
                            echo '<td>✔️</td>'; // Cumple
                        } else {
                            echo '<td>❌</td>'; // No cumple
                        }

                        echo '<td>' . htmlspecialchars($row['vacante']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['fecha_registro']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['ubicacion']) . '</td>';
                        echo '<td>';
                        if (!empty($row['archivo_documento'])) {
                            echo '<a href="' . htmlspecialchars($row['archivo_documento']) . '" target="_blank">Ver Hoja de Vida</a>';
                        } else {
                            echo 'No disponible';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="9" class="no-records">Sin registros disponibles</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    require_once 'includes/footer.php';
    ?>
</body>

</html>

<?php
$conn->close();
?>



