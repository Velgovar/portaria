<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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
    <link rel="icon" href="../images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/menu.css">
    </head>
<body>
<a href="config/logout.php" class="style">Sair</a>
    <div class="title">Bem-vindo ao Modo Administrador</div>
    <div class="container">
         <nav>
            <div class="nav-column">
                <ul>
                    <li><a href="tabela_veiculos.php">Cadastros veiculos da empresa</a></li>
                    <li><a href="porteiros.php">Cadastrar porteiros</a></li>
                    <li><a href="tipo.php">Cadastrar tipo de visitante</a></li>
               </ul>
            </div>
            <div class="nav-column">
                <ul>
                <li><a href="tabela_visitantes.php">Cadastros de visitantes</a></li>
                <li><a href="setores.php">Cadastrar setores</a></li>
                    <li><a href="veiculos.php">Cadastrar veiculos</a></li>
                </ul>
            </div>
        </nav>
    </div> 
</body>
</html>

