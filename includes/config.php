<?php
// includes/config.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pae2024";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}