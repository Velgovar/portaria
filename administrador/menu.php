<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: black;
    color: white;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-size: cover;
    background-position: center;
    position: relative;
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


.container {
    background-color: rgba(51, 51, 51, 0.9);
    border-radius: 4%;
    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                5px 5px 20px rgba(0, 0, 0, 0.2),
                10px 10px 25px rgba(0, 0, 0, 0.1);
    padding: 40px;
    text-align: center;
    width: 100%;
    max-width: 900px;
    position: relative;
    margin-top: 50px; /* Ajustado para mover o container mais para cima */
    margin-bottom: 50px; /* Adicionado para dar um espaço em baixo do container */
}

header h1 {
    margin: 0;
    font-size: 2em;
    letter-spacing: 2px;
    color: #f2f2f2;
}

nav {
    display: flex;
    justify-content: space-between;
}

.nav-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 50%;
}

.nav-column ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-column li {
    margin: 10px 0;
}

.nav-column li a {
    display: block;
    color: #f2f2f2;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.2em;
    padding: 15px 25px;
    border-radius: 10px;
    background-color: rgba(0, 0, 0, 0.7); /* Escurece o fundo dos botões */
    transition: background-color 0.3s, transform 0.3s;
    width: 100%; /* Garante que todos os botões tenham a mesma largura */
    text-align: center;
}

.nav-column li a:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.settings-icon {
    position: fixed;
    top: 20px;
    left: 20px;
    font-size: 2.5em;
    color: black; /* Define a cor preta para o ícone */
    text-shadow: 1px 1px 3px white; /* Adiciona uma sombra branca */
    cursor: pointer;
    transition: color 0.3s, transform 0.3s;
    z-index: 1001; /* Garante que o ícone fique sobre outros elementos */
}

.settings-icon:hover {
    color: gray; /* Fica cinza ao passar o mouse */
    transform: scale(1.1);
}


.admin-launcher {
    display: none;
    position: fixed;
    top: 20px;
    left: 80px; /* Ajuste a posição horizontal conforme necessário */
    background-color: rgba(51, 51, 51, 0.9);
    border-radius: 4%;
    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                5px 5px 20px rgba(0, 0, 0, 0.2),
                10px 10px 25px rgba(0, 0, 0, 0.1);
    color: white;
    padding: 20px;
    font-size: 1.2em;
    cursor: pointer;
    transition: transform 0.3s;
    z-index: 1000; /* Garante que o painel fique sobre outros elementos */
    width: 220px; /* Ajuste a largura do painel */
    text-align: left;
}

.admin-launcher div {
    padding: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.admin-launcher div:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.admin-launcher .admin,
.admin-launcher .ramais,
.admin-launcher .ajuda {
    font-size: 1.2em;
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

/* Estilos do Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1002; /* Certifica-se que o modal fique acima dos outros elementos */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9); /* Fundo escuro semitransparente */
    background-size: cover;
    background-position: center;
}

.modal-content {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: transparent; /* Fundo transparente para permitir a imagem de fundo */
    border: none;
    padding: 0;
    margin: 0;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Remover o botão de fechar do modal */
.close {
    display: none;
}

iframe {
    width: 100%;
    height: calc(100% - 40px); /* Ajuste para deixar espaço para o botão de fechar */
    border: none;
}
    </style>
</head>
<body>
<a href="config/logout.php" class="style">Sair</a>
    <div class="title">Bem-vindo ao Modo Administrador</div>
    <div class="container">
        <header>
            <!-- Removido o h1 de dentro da div com a mensagem -->
        </header>
        <nav>
            <div class="nav-column">
                <ul>
                    <li><a href="tabela_veiculos.php">Cadastros de Veiculos</a></li>
                    <li><a href="porteiros.php">Cadastrar Porteiros</a></li>
                    <li><a href="tipo.php">Cadastrar Tipo de Visitante</a></li>

                </ul>
            </div>
            <div class="nav-column">
                <ul>
                <li><a href="tabela_visitantes.php">Cadastros de Visitantes</a></li>
                <li><a href="setores.php">Cadastrar Setores</a></li>
                    <li><a href="veiculos.php">Cadastrar Veiculos</a></li>
                </ul>
            </div>
        </nav>
    </div> 
</body>
</html>
