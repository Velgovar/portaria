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

    $nome = $conn->real_escape_string($_POST['nome']);

    $sql = "INSERT INTO porteiros (nome) VALUES ('$nome')";
    $sql = "INSERT INTO setores (nome) VALUES ('$nome')";


    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        echo json_encode(['status' => 'success', 'message' => 'Vigia cadastrado com sucesso!', 'id' => $last_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar vigia: ' . $conn->error]);
    }

    $conn->close();
}
?>
