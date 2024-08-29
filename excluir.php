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

    // Exclusão da tabela porteiros
    $sql = "DELETE FROM porteiros WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Registro excluído com sucesso.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o registro: ' . $conn->error]);
    }

    // Fecha a conexão
    $conn->close();
}
?>
