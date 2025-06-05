<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Profesional</title>
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/login.css">
</head>
<body>
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>

    <div class="container">
        <div class="logo">
            <h1>REMOTE ORDER</h1>
        </div>

        <div class="tabs">
            <div class="tab-indicator" id="tab-indicator"></div>
            <div class="tab active" onclick="switchTab('login')">Iniciar Sesión</div>
            <div class="tab" onclick="switchTab('signup')">Registrarse</div>
        </div>

        <div id="login-form" class="form-container active">
            <form action="../actions/iniciarSesion.php" method="POST">
            <div class="form-group">
                <label for="login-email">Correo electrónico</label>
                <input type="email" id="login-email" name="login-email" class="form-control" placeholder="correo@empresa.com" required>
            </div>

            <div class="form-group">
                <label for="login-password">Contraseña</label>
                <input type="password" id="login-password" name="login-password" class="form-control" placeholder="••••••••" required>
            </div>

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
        </div>

        <div id="signup-form" class="form-container">
            <form action="../actions/registrar.php" method="POST">
                <div class="form-group">
                    <label for="signup-name">Nombre completo</label>
                    <input type="text" id="signup-name" name="signup-name" class="form-control" placeholder="Tu nombre completo" required>
                </div>

                <div class="form-group">
                    <label for="signup-email">Correo electrónico</label>
                    <input type="email" id="signup-email" name="signup-email" class="form-control" placeholder="correo@empresa.com" required>
                </div>

                <div class="form-group">
                    <label for="signup-password">Contraseña</label>
                    <input type="password" id="signup-password" name="signup-password" class="form-control" placeholder="Crea una contraseña segura" required>
                </div>

                <div class="form-group">
                    <label for="signup-confirm-password">Confirmar contraseña</label>
                    <input type="password" id="signup-confirm-password" name="signup-confirm-password" class="form-control" placeholder="Confirma tu contraseña" required>
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Acepto los términos y condiciones</label>
                </div>

                <button type="submit" class="btn">Crear cuenta</button>
            </form>
        </div>
    </div>

    <script src="../../public/js/login.js"></script>
</body>
</html>