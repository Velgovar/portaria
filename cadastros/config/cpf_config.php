<?php
// Configura exibição de erros para ajudar no debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações de conexão com o banco de dados
$servername = "172.16.0.225";
$username = "root";
$password = "Meunome1@";
$dbname = "portaria";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die(json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]));
}

// Verifica se o parâmetro CPF foi fornecido e não está vazio
if (isset($_GET['cpf']) && !empty($_GET['cpf'])) {
    $cpf = $_GET['cpf'];

    // Debug: Mostra o CPF recebido
    // REMOVE isso antes de colocar em produção
    // echo json_encode(['received_cpf' => $cpf]);

    // Protege contra SQL Injection
    $cpf = $conn->real_escape_string($cpf);

    // Consulta SQL
    $sql = "SELECT * FROM registro WHERE cpf = '$cpf'";
    $result = $conn->query($sql);

    // Verifica se encontrou resultados
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Nenhum dado encontrado para o CPF fornecido.']);
    }
} else {
    echo json_encode(['error' => 'CPF não fornecido.']);
}

// Fecha a conexão com o banco de dados
$conn->close();
