<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "192.168.254.136";
    $username = "felipe";
    $password = "Aranhas12@";
    $dbname = "cobra";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]));
    }

    $id = $conn->real_escape_string($_POST['id']);

    $sql = "DELETE FROM porteiros WHERE id = $id";
    $sql = "DELETE FROM setores WHERE id = $id";


    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Vigia excluído com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir vigia: ' . $conn->error]);
    }

    $conn->close();
}
?>
