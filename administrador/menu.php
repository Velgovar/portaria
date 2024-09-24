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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/menu.css">
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

