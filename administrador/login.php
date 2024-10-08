<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../images/favicon.ico" type="image/png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    
    <div class="title">login</div>

    <a href="../menu.html" class="style">Voltar</a>

    <div class="container">
        <form action="config/login_config.php" method="post">
            <div class="form-group">
                <label for="username">USUÁRIO</label>
                <input type="text" name="username" id="username" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">SENHA</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" autocomplete="off">
                    <span id="togglePassword" class="toggle-password">
                        <i id="eyeIcon" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" id="submit-btn" class="entrar">Login</button>
            </div>
        </form>

        <div id="launcher" class="launcher hidden">
            <div class="launcher-message"></div>
        </div>
    </div>

    <script>
        // Verifica se a mensagem de erro está presente
        const launcher = document.getElementById('launcher');
        const launcherMessage = document.querySelector('.launcher-message');

        // Se houver uma mensagem de erro de login, mostre o launcher
        <?php if (isset($_SESSION['login_error'])): ?>
            launcherMessage.textContent = "<?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>";
            launcher.classList.remove('hidden');
            
            // Oculta o launcher após 1.5 segundos
            setTimeout(() => {
                launcher.classList.add('hidden');
            }, 1500);
        <?php endif; ?>

        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            // Alterna o tipo de input entre 'password' e 'text'
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Alterna o ícone entre olho aberto e fechado
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>
