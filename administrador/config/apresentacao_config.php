<?php
header('Content-Type: application/json');

// Incluir o arquivo de configuração do banco de dados
require '../../db_config.php';

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    // Conectar ao banco de dados usando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar se é uma exclusão
        if (isset($_POST['confirmacao']) && $_POST['confirmacao'] === 'excluir' && isset($_POST['id'])) {
            $id = $_POST['id'];

            // Preparar a consulta de exclusão
            $sql = "DELETE FROM apresentacao WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            // Executar a consulta
            if ($stmt->execute([':id' => $id])) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Registro excluído com sucesso.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Nenhum registro encontrado para excluir.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao executar a exclusão.']);
            }
        }

        // Verificar se é uma atualização completa
        elseif (
            isset($_POST['id']) && isset($_POST['data']) && isset($_POST['porteiro']) &&
            isset($_POST['horario_apresentacao']) && isset($_POST['nome']) && isset($_POST['empresa'])
        ) {
            $id = $_POST['id'];
            $data = $_POST['data'];
            $porteiro = $_POST['porteiro'];
            $horario_apresentacao = $_POST['horario_apresentacao'];
            $nome = $_POST['nome'];
            $empresa = $_POST['empresa'];

            // Preparar a consulta de atualização completa
            $stmt = $pdo->prepare("UPDATE apresentacao SET
                data = :data,
                porteiro = :porteiro,
                horario_apresentacao = :horario_apresentacao,
                nome = :nome,
                empresa = :empresa
                WHERE id = :id");

            // Vincular parâmetros
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':porteiro', $porteiro);
            $stmt->bindParam(':horario_apresentacao', $horario_apresentacao);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':empresa', $empresa);

            // Executar a consulta
            $stmt->execute();

            echo json_encode(['success' => true]);
        }

        // Caso não corresponda a nenhuma das operações anteriores
        else {
            echo json_encode(['success' => false, 'message' => 'Dados insuficientes ou inválidos fornecidos.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()]);
    exit();
}
