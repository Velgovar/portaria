<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "192.168.254.136";
    $username = "felipe";
    $password = "Aranhas12@";
    $dbname = "cobra";

    // Criação da conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificação da conexão
    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]));
    }

    if (isset($_POST['id'])) {
        // Exclusão da tabela veiculos
        $id = intval($_POST['id']);
        $sql = "DELETE FROM veiculos WHERE id = $id";
        $result = $conn->query($sql);

        // Verificação do resultado
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Setor excluído com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir setor: ' . $conn->error]);
        }
    } elseif (isset($_POST['nome'])) {
        // Inserção na tabela veiculos
        $nome = $conn->real_escape_string($_POST['nome']);
        $sql = "INSERT INTO veiculos (nome) VALUES ('$nome')";
        $result = $conn->query($sql);

        // Verificação do resultado
        if ($result) {
            $last_id = $conn->insert_id;
            echo json_encode(['status' => 'success', 'message' => 'Setor cadastrado com sucesso!', 'id' => $last_id, 'nome' => $nome]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar setor: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Dados de entrada inválidos.']);
    }

    // Fechamento da conexão
    $conn->close();
}
?>

