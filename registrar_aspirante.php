<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Aspirante</title>
    <!-- Puedes incluir tus estilos CSS aquí -->
    <style>
    /* Estilos para mensajes de alerta */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    /* Estilos para el formulario */
    .container {
        width: 90%;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #28a745;
        text-align: center;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-top: 10px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        box-sizing: border-box;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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
        background-color: #9ccc65;
        color: white;
    }

    .button-container {
        text-align: center;
        margin-top: 20px;
    }

    .button-container button {
        padding: 10px 20px;
        background-color: #28a745;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 5px;
    }

    .button-container button:hover {
        background-color: #218838;
    }

    .result-message.not-eligible {
        color: #d9534f;
        font-weight: bold;
        text-align: center;
    }
    </style>
</head>

<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="container">
        <h2>Formulario de Registro de Aspirante</h2>

        <!-- Mostrar mensajes de sesión -->
        <?php
        if (isset($_SESSION['message'])) {
            $message_type = $_SESSION['message_type'] == "success" ? "alert-success" : "alert-danger";
            echo "<div class='alert $message_type'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <form id="aspirante-form" action="procesar_aspirante.php" method="post" enctype="multipart/form-data">
            <!-- Campos de texto -->
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="tipo_identificacion">Tipo de Identificación:</label>
            <select id="tipo_identificacion" name="tipo_identificacion" required>
                <option value="">Seleccione el tipo</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="CE">Cédula de Extranjería</option>
                <option value="NIT">NIT</option>
                <option value="Pasaporte">Pasaporte</option>
            </select>

            <label for="numero_identificacion">Número de Identificación:</label>
            <input type="text" id="numero_identificacion" name="numero_identificacion" required>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="telefono">Número de Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" required>

            <label for="municipio">Municipio:</label> <!-- Nuevo campo -->
            <input type="text" id="municipio" name="ubicacion" required> <!-- Campo de ubicación -->

            <label for="vacante">Vacante a la que Aspira:</label>
            <select id="vacante" name="vacante" required onchange="actualizarRequisitoExperiencia()">
                <option value="">Seleccione la vacante</option>
                <?php
                // Obtener vacantes de la base de datos
                $query_vacantes = "SELECT id, titulo, total_meses_requeridos FROM vacantes";
                $result_vacantes = $conn->query($query_vacantes);

                if ($result_vacantes->num_rows > 0) {
                    while ($vacante_row = $result_vacantes->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($vacante_row['titulo']) . '" data-experiencia="' . htmlspecialchars($vacante_row['total_meses_requeridos']) . '">' . htmlspecialchars($vacante_row['titulo']) . '</option>';
                    }
                } else {
                    echo '<option value="">No hay vacantes disponibles</option>';
                }
                ?>
            </select>

            <!-- Campo oculto para almacenar el requisito de experiencia mínima de la vacante seleccionada -->
            <input type="hidden" id="requisito_experiencia" name="requisito_experiencia" value="">

            <label for="anos_experiencia">Años de Experiencia:</label>
            <input type="number" id="anos_experiencia" name="anos_experiencia" min="0" required
                oninput="calcularTotalExperiencia()">

            <label for="meses_experiencia">Meses de Experiencia:</label>
            <input type="number" id="meses_experiencia" name="meses_experiencia" min="0" max="11"
                oninput="calcularTotalExperiencia()">

            <label for="total_experiencia_meses">Total Experiencia (en meses):</label>
            <input type="number" id="total_experiencia_meses" name="total_experiencia_meses" readonly>

            <label for="apto">Estado de Aptitud:</label>
            <select id="apto" name="apto" required>
                <option value="">Seleccione una opción</option>
                <option value="habilitado">Habilitado</option>
                <option value="ponderado">Ponderado</option>
            </select>

            <h3>Documentos a Cargar</h3>
            <!-- Tabla de Carga de Documentos -->
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Documento</th>
                        <th>Cargar Documento</th>
                        <th>Cumple</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hoja de vida formato función pública</td>
                        <td><input type="file" name="hoja_vida" class="upload-input"></td>
                        <td><input type="checkbox" name="verificar_hoja_vida" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Carta de intención</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_carta_intencion" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Fotocopia de cédula</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_fotocopia_cedula" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Libreta militar (Si Aplica)</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_libreta_militar" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Certificado de Vecindad o Sisben</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_cert_residencia" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Frotis de Garganta y KOH para Uñas (>6m)</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_cert_examen" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Certificado RNMC</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_cert_RNMC" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Curso de Higiene y Manejo de alimentos</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_curso_higiene" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Curso Protección y Conservación</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_curso_proteccion" class="verify-checkbox"></td>
                    </tr>
                    <tr>
                        <td>Experiencia en PAE</td>
                        <td>-</td>
                        <td><input type="checkbox" name="verificar_experiencia_pae" class="verify-checkbox"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Botón para enviar el formulario -->
            <div class="button-container">
                <button type="submit" name="submit">Guardar Aspirante</button>
            </div>
        </form>
    </div>

    <!-- Scripts para calcular la experiencia -->
    <script>
    function calcularTotalExperiencia() {
        const anos = parseInt(document.getElementById('anos_experiencia').value) || 0;
        const meses = parseInt(document.getElementById('meses_experiencia').value) || 0;

        // Asegurarse de que los meses no excedan de 11
        if (meses > 11) {
            alert("Los meses de experiencia no pueden exceder 11.");
            document.getElementById('meses_experiencia').value = 11;
            return;
        }

        const totalMeses = (anos * 12) + meses;
        document.getElementById('total_experiencia_meses').value = totalMeses;

        validarExperiencia(totalMeses);
    }

    function validarExperiencia(totalMeses) {
        const requisito = parseInt(document.getElementById('requisito_experiencia').value) || 0;

        const mensaje = document.getElementById('result-message');
        if (mensaje) {
            mensaje.remove();
        }

        if (totalMeses < requisito && requisito > 0) {
            const mensajeDiv = document.createElement('div');
            mensajeDiv.id = 'result-message';
            mensajeDiv.classList.add('result-message', 'not-eligible');
            mensajeDiv.innerHTML = "NO APTO PARA EL CARGO";
            document.querySelector('.container').appendChild(mensajeDiv);
        }
    }

    function actualizarRequisitoExperiencia() {
        const selectVacante = document.getElementById('vacante');
        const selectedOption = selectVacante.options[selectVacante.selectedIndex];
        const requisitoExperiencia = parseInt(selectedOption.getAttribute('data-experiencia')) || 0;
        document.getElementById('requisito_experiencia').value = requisitoExperiencia;

        // Recalcular la experiencia total para validar
        calcularTotalExperiencia();
    }
    </script>
</body>

</html>
