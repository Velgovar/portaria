<<<<<<< HEAD
<?php
header('Content-Type: application/json');

$host = '172.16.0.225';
$dbname = 'portaria';
$username = 'root';
$password = 'Meunome1@';

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o ID do registro a ser excluído
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Preparar a consulta SQL para excluir o registro
        $stmt = $pdo->prepare("DELETE FROM registro WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Executar a consulta
        $stmt->execute();

        // Verificar se a exclusão foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Registro excluído com sucesso.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nenhum registro encontrado para excluir.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
    }
} catch (PDOException $e) {
    // Capturar e exibir erros de conexão
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
}
=======
<?php
header('Content-Type: application/json');

$host = '192.168.254.136';
$dbname = 'cobra';
$username = 'felipe';
$password = 'Aranhas12@';

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o ID do registro a ser excluído
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Preparar a consulta SQL para excluir o registro
        $stmt = $pdo->prepare("DELETE FROM registro WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Executar a consulta
        $stmt->execute();

        // Verificar se a exclusão foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Registro excluído com sucesso.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nenhum registro encontrado para excluir.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
    }
} catch (PDOException $e) {
    // Capturar e exibir erros de conexão
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
}
>>>>>>> origin/master
