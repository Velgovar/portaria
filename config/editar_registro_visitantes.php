<?php
header('Content-Type: application/json');

// Configurações de conexão com o banco de dados
$host = '192.168.254.136';
$dbname = 'cobra';
$username = 'felipe';
$password = 'Aranhas12@';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $id = $_POST['id'];
    $horario_saida = $_POST['horario_saida'];

    // Prepara a consulta SQL para atualizar o horário de saída
    $sql = "UPDATE registro SET horario_saida = :horario_saida WHERE id = :id";

    // Prepara a instrução SQL para execução
    $stmt = $pdo->prepare($sql);

    // Executa a instrução SQL
    try {
        $stmt->execute(array(
            ':horario_saida' => $horario_saida,
            ':id' => $id,
        ));
        echo json_encode(array('message' => 'Registro atualizado com sucesso!'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('message' => 'Erro ao atualizar o registro: ' . $e->getMessage()));
    }
}
?>
