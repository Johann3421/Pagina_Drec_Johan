<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- ===== BOX ICONS ===== -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

    <title>Login form responsive</title>
</head>

<body>
    <div class="l-form">
        <!-- Form container for logo and form -->
        <div class="form-container">
            <!-- Logo section on the left -->
            <div class="logo-container">
                <img src="./imagenes/logo_principal.jpeg" alt="Logo">
            </div>

            <!-- Form section on the right -->
            <div class="form">
                <!-- Form points to login.php -->
                <form action="login.php" method="POST" class="form__content">
                    <h1 class="form__title">Bienvenido</h1>

                    <div class="form__div form__div-one">
                        <div class="form__icon">
                            <i class='bx bx-user-circle'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="username" class="form__label">Usuario</label>
                            <input type="text" class="form__input" name="username" id="username" required>
                        </div>
                    </div>

                    <div class="form__div">
                        <div class="form__icon">
                            <i class='bx bx-lock'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="password" class="form__label">Contraseña</label>
                            <input type="password" class="form__input" name="password" id="password" required>
                        </div>
                    </div>

                    <input type="submit" class="form__button" value="Login">
                    
                </form>
            </div>
        </div>
    </div>

    <!-- ===== MAIN JS ===== -->
    <script src="assets/js/main.js"></script>
</body>
</html>
