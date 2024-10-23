<?php
// login.php
session_start();
require_once 'includes/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pin = $_POST['pin'];

    // Validar el PIN
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE pin = ?");
    $stmt->bind_param("s", $pin);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $_SESSION['loggedin'] = true;
        header("location: index.php");
    } else {
        $message = "Contraseña incorrecta,  intentelo nuevamente, o comuniquese con soporte técnico.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - PAE 2025</title>
    <style>
    /* Estilo para centrar toda la página */
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
    }

    /* Contenedor del formulario */
    .login-container {
        background-color: #fff;
        padding: 60px;
        border-radius: 15px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    h2 {
        text-align: center;
        color: #4CAF50;
        margin-bottom: 20px;
        font-size: 24px;
    }

    form {
        display: center;
        flex-direction: column;
        width: 100%;
    }

    label {
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    input[type="password"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #45a049;
    }

    .error {
        color: red;
        text-align: center;
        font-size: 14px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Ingreso al Sistema</h2>
        <?php if($message): ?>
        <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="pin">PIN de Acceso:</label>
            <input type="password" id="pin" name="pin" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>

</html>