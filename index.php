<?php
// login.php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar usuario por correo electrónico
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header('Location: welcome.php');
        exit();
    } else {
        $error = 'Credenciales inválidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fondo con imagen y overlay gris */
        body {
            background-image: url('././imagenes/Imagen1.png'); /* Imagen de fondo */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            position: relative; /* Para que el overlay funcione */
        }

        /* Overlay gris para hacer que el fondo se vea gris */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(211, 211, 211, 0.7); /* Color gris claro con opacidad */
            z-index: 0; /* Debe estar debajo del contenido */
        }

        /* Contenedor principal */
        .main-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            height: 80vh;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            z-index: 1; /* Aseguramos que esté por encima del overlay */
            position: relative;
        }

        /* Contenedor para las imágenes de logo */
        .logo-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: rgba(128, 191, 255, 0.8);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        /* Estilo para las imágenes */
        .logo-container img {
            max-width: 600px;
            margin-bottom: 20px;
        }

        /* Contenedor para el formulario */
        .login-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Estilo del formulario */
        .login-container h2 {
            margin-bottom: 20px;
        }

        /* Hacer el diseño responsivo */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                height: auto;
            }

            .logo-container {
                border-radius: 10px 10px 0 0;
                padding: 20px;
            }

            .login-container {
                border-radius: 0 0 10px 10px;
            }
        }
    </style>
    <title>Login</title>
</head>

<body>
    <div class="main-container">
        <!-- Contenedor para las imágenes de logo -->
        <div class="logo-container">
            <img src="././imagenes/logodiseno.png" alt="Logo 1">
        </div>

        <!-- Contenedor para el formulario de login -->
        <div class="login-container">
            <h2>Iniciar sesión</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
            <p class="mt-3 text-center">¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
