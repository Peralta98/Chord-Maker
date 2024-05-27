<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "musica";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
