<?php
// db.php
$host = 'localhost';
$dbname = 'login_system';
$user = 'root'; // Cambia esto si tu usuario de MySQL es diferente
$pass = '';     // Cambia esto si tu contraseña es diferente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>
