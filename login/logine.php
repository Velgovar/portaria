<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/veiculos.css">

    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .container {
            width: 100%;
            max-width: 400px;
            background-color: rgba(51, 51, 51, 0.9);
            border-radius: 4%;
            padding: 20px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                5px 5px 20px rgba(0, 0, 0, 0.2),
                10px 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #fff;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ddd;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            background-color: #444;
            color: #fff;
            border: 1px solid #555;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #66afe9;
            outline: none;
        }

        .button-container {
            margin-top: 20px;
        }

        .style {
    position: absolute;
    top: 10px;
    left: 10px;
    border: 0;
    color: white;
    line-height: 1.75; /* Ajustado para um pouco mais de altura */
    padding: 0 40px; /* Aumentado um pouco */
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.95rem; /* Ajustado para um pouco maior */
    border-radius: 10px; /* Ajustado para um pouco maior */
    cursor: pointer;
    background-color: rgb(0, 138, 143);
    background-image: linear-gradient(
      to top left,
        rgb(0 0 0 / 20%),
        rgb(0 0 0 / 20%) 30%,
        rgb(0 0 0 / 0%));
    box-shadow:
      inset 2px 2px 3px rgb(255 255 255 / 60%),
      inset -2px -2px 3px rgb(0 0 0 / 60%);
}

.style:hover {
    background-color: rgb(0, 209, 216);
}

.style:active {
    box-shadow:
      inset -2px -2px 3px rgb(255 255 255 / 60%),
      inset 2px 2px 3px rgb(0 0 0 / 60%);
}


        .error-message,
        .success-message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            display: none;
            color: #fff;
        }

        .error-message {
            background-color: #d9534f;
        }

        .success-message {
            background-color: #5bc0de;
        }

                /* Seus estilos existentes aqui */
                .error-message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            display: <?php echo isset($_SESSION['login_error']) ? 'block' : 'none'; ?>;
            color: #fff;
            background-color: #d9534f;
        }

        .password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #fff; /* Branco para o olho */
}

.toggle-password i {
    font-size: 18px; /* Ajuste o tamanho conforme necessário */
}

.title {
    position: absolute;
    top: 245px; /* Ajuste conforme necessário */
    left: 50%;
    transform: translateX(-50%);
    font-size: 3em;
    font-weight: bold;
    color: #666; /* Texto preto */
    text-shadow: 
        0.6px 0.6px 0 rgba(255, 255, 255, 0.8),
        1px 1px 0 rgba(255, 255, 255, 0.7),
        1.5px 1.5px 0 rgba(255, 255, 255, 0.6),
        2px 2px 0 rgba(255, 255, 255, 0.5); /* Sombra branca suave */
    z-index: 1000; /* Garante que o título fique sobre outros elementos */
}

    </style>
</head>
<body>
<div class="title">login</div>

    <a href="../menu.html" class="style">Voltar</a>

    <div class="container">
        <form action="login.php" method="post">
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
                <button type="submit" id="submit-btn" class="styled">Login</button>
            </div>
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-message" id="error-message"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        // Verifica se a mensagem de erro está presente
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            // Espera 1.5 segundos e depois oculta a mensagem
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 1500);
        }

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



