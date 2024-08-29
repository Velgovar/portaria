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

    // Sanitização do input
    $nome = $conn->real_escape_string($_POST['nome']);

    // Inserção na tabela porteiros
    $sql1 = "INSERT INTO porteiros (nome) VALUES ('$nome')";
    $result1 = $conn->query($sql1);

    // Inserção na tabela setores
    $sql2 = "INSERT INTO setores (nome) VALUES ('$nome')";
    $result2 = $conn->query($sql2);

    // Verificação dos resultados
    if ($result1 && $result2) {
        $last_id = $conn->insert_id;
        echo json_encode(['status' => 'success', 'message' => 'Vigia cadastrado com sucesso!', 'id' => $last_id]);
    } else {
        // Tratamento de erro para cada consulta
        $errors = [];
        if (!$result1) {
            $errors[] = 'Erro ao cadastrar vigia: ' . $conn->error;
        }
        if (!$result2) {
            $errors[] = 'Erro ao cadastrar setor: ' . $conn->error;
        }
        echo json_encode(['status' => 'error', 'message' => implode(' | ', $errors)]);
    }

    // Fechamento da conexão
    $conn->close();
}
?>
