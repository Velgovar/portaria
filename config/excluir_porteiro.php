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
    $id = $conn->real_escape_string($_POST['id']);

    // Deleção na tabela porteiros
    $sql1 = "DELETE FROM porteiros WHERE id = $id";
    $result1 = $conn->query($sql1);

    // Deleção na tabela setores
    $sql2 = "DELETE FROM setores WHERE id = $id";
    $result2 = $conn->query($sql2);

    // Verificação dos resultados
    if ($result1 && $result2) {
        echo json_encode(['status' => 'success', 'message' => 'Vigia excluído com sucesso!']);
    } else {
        // Tratamento de erro para cada consulta
        $errors = [];
        if (!$result1) {
            $errors[] = 'Erro ao excluir da tabela porteiros: ' . $conn->error;
        }
        if (!$result2) {
            $errors[] = 'Erro ao excluir da tabela setores: ' . $conn->error;
        }
        echo json_encode(['status' => 'error', 'message' => implode(' | ', $errors)]);
    }

    // Fechamento da conexão
    $conn->close();
}
?>
