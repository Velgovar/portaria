<?php
session_start();

// Configurações de conexão com o banco de dados
$dsn = 'mysql:host=172.16.0.225;dbname=portaria';
$dbUsername = 'root';
$dbPassword = 'Meunome1@';

// Conexão com o banco de dados
try {
    $pdo = new PDO($dsn, $dbUsername, $dbPassword);
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
        // Armazena a mensagem de erro na sessão e redireciona de volta para logine.php
        $_SESSION['login_error'] = 'Senha Inválida';
        header('Location: ../login.php');
        exit;
    }
}



