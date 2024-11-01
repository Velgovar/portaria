<?php
session_start();

// Inclui as configurações do banco de dados
require '../../db_config.php';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para buscar o usuário
    $query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $query->execute(['username' => $username]);
    $user = $query->fetch();

    // Verifica se o usuário foi encontrado e a senha está correta
    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ../menu.php');
        exit;
    } else {
        // Armazena a mensagem de erro na sessão e redireciona de volta para login.php
        $_SESSION['login_error'] = 'Usuário ou senha Inválido';
        header('Location: ../login.php');
        exit;
    }
}
?>
