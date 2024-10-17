<?php
// login.php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];  // Recibe el username del formulario
    $password = $_POST['password'];  // Recibe la contraseña del formulario

    // Buscar usuario por nombre de usuario
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Si la contraseña es correcta, inicia sesión
        $_SESSION['username'] = $user['username'];
        header('Location: welcome.php');  // Redirigir al área protegida
        exit();
    } else {
        $error = 'Credenciales inválidas.';
    }
}
?>
