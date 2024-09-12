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
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar se é uma exclusão
        if (isset($_POST['confirmacao']) && $_POST['confirmacao'] === 'excluir' && isset($_POST['id'])) {
            $id = $_POST['id'];

            $sql = "DELETE FROM registros_veiculos WHERE id = :id";
            $stmt = $pdo->prepare($sql);

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
            isset($_POST['veiculo']) && isset($_POST['km_saida']) && isset($_POST['km_chegada']) &&
            isset($_POST['horario_saida']) && isset($_POST['horario_chegada']) &&
            isset($_POST['destino']) && isset($_POST['motivo'])
        ) {
            $id = $_POST['id'];
            $data = $_POST['data'];
            $porteiro = $_POST['porteiro'];
            $veiculo = $_POST['veiculo'];
            $km_saida = $_POST['km_saida'];
            $km_chegada = $_POST['km_chegada'];
            $horario_saida = $_POST['horario_saida'];
            $horario_chegada = $_POST['horario_chegada'];
            $destino = $_POST['destino'];
            $motivo = $_POST['motivo'];

            $stmt = $pdo->prepare("UPDATE registros_veiculos SET
                data = :data,
                porteiro = :porteiro,
                veiculo = :veiculo,
                km_saida = :km_saida,
                km_chegada = :km_chegada, 
                horario_saida = :horario_saida,
                horario_chegada = :horario_chegada, 
                destino = :destino,
                motivo = :motivo
                WHERE id = :id");

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':porteiro', $porteiro);
            $stmt->bindParam(':veiculo', $veiculo);
            $stmt->bindParam(':km_saida', $km_saida);
            $stmt->bindParam(':km_chegada', $km_chegada);
            $stmt->bindParam(':horario_saida', $horario_saida);
            $stmt->bindParam(':horario_chegada', $horario_chegada);
            $stmt->bindParam(':destino', $destino);
            $stmt->bindParam(':motivo', $motivo);

            $stmt->execute();

            echo json_encode(['success' => true]);
        }

        // Verificar se é uma atualização parcial
        elseif (isset($_POST['id']) && isset($_POST['km_chegada']) && isset($_POST['horario_chegada'])) {
            $id = $_POST['id'];
            $km_chegada = $_POST['km_chegada'];
            $horario_chegada = $_POST['horario_chegada'];

            $stmt = $pdo->prepare("UPDATE registros_veiculos SET km_chegada = :km_chegada, horario_chegada = :horario_chegada WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':km_chegada', $km_chegada);
            $stmt->bindParam(':horario_chegada', $horario_chegada);
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
