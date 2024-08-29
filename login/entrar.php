<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: logine.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../css/menu.css">
    <style>
        /* Estilos para o botão de voltar */
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

    </style>
</head>

<body>
    <!-- Botão de Voltar ao Menu -->
    <a href="logout.php" class="style">Sair</a>
    <header>
        <h1>Bem-vindo ao Painel de administrador</h1>
        <nav>
            <ul>
                <li><a href="../login/tabela_veiculos.php">Cadastros de veiculos</a></li>
                <li><a href="../login/cadastros_visitantes.php">Cadastros de Visitantes</a></li>
                <li><a href="../login/porteiros.php">Cadastros de porteiros</a></li>
                <li><a href="../login/setores.php">Cadastros de Setores</a></li>

                <!-- Adicione mais links conforme necessário -->
            </ul>
        </nav>
    </header>
</body>
</html>
