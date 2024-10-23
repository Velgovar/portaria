<?php
header('Content-Type: application/json');

// Inclui as configurações do banco de dados
require '../../db_config.php';

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(array('message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()));
    exit;
}

// Função para validar dados e garantir que não seja null
function validate_data($data) {
    return htmlspecialchars(strip_tags($data)) ?: ''; // Retorna string vazia se for null
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe e valida o ID
    $id = isset($_POST['id']) ? validate_data($_POST['id']) : '';

    // Se o ID estiver presente, verifica se os dados para atualização também estão presentes
    if ($id !== '' && isset($_POST['data'])) {
        // Recebe e valida os dados do formulário, substituindo null por strings vazias
        $data = validate_data($_POST['data']);
        $porteiro = validate_data($_POST['porteiro']);
        $nome = validate_data($_POST['nome']);
        $cpf = validate_data($_POST['cpf']); // CPF tratado como varchar(14)
        $tipovisitante = validate_data($_POST['tipovisitante']);
        $servico = validate_data($_POST['servico']);
        $empresa = validate_data($_POST['empresa']);
        $estacionamento = validate_data($_POST['estacionamento']);
        $placa = validate_data($_POST['placa']);
        $horarioEntrada = validate_data($_POST['horario_entrada']);
        $horarioSaida = validate_data($_POST['horario_saida']);
        $colaborador = validate_data($_POST['colaborador']);
        $setor = validate_data($_POST['setor']);

        // Prepara a consulta SQL para atualizar os dados
        $sql = "UPDATE registro SET 
                    data = :data, 
                    porteiro = :porteiro, 
                    nome = :nome, 
                    cpf = :cpf,  /* Campo CPF como varchar(14) */
                    tipovisitante = :tipovisitante, 
                    servico = :servico, 
                    empresa = :empresa, 
                    estacionamento = :estacionamento, 
                    placa = :placa, 
                    horario_entrada = :horario_entrada, 
                    horario_saida = :horario_saida, 
                    colaborador = :colaborador, 
                    setor = :setor 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute(array(
                ':data' => $data,
                ':porteiro' => $porteiro,
                ':nome' => $nome,
                ':cpf' => $cpf, // CPF tratado como varchar(14)
                ':tipovisitante' => $tipovisitante,
                ':servico' => $servico,
                ':empresa' => $empresa,
                ':estacionamento' => $estacionamento,
                ':placa' => $placa,
                ':horario_entrada' => $horarioEntrada,
                ':horario_saida' => $horarioSaida,
                ':colaborador' => $colaborador,
                ':setor' => $setor,
                ':id' => $id,
            ));

            echo json_encode(array('status' => 'success', 'message' => 'Registro atualizado com sucesso!'));
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(array('status' => 'error', 'message' => 'Erro ao atualizar o registro: ' . $e->getMessage()));
        }
    } elseif ($id !== '' && !isset($_POST['data'])) {
        // Se não houver dados para atualização, tenta excluir o registro
        if ($id > 0) {
            // Preparar a consulta SQL para excluir o registro
            $stmt = $pdo->prepare("DELETE FROM registro WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Executar a consulta
            $stmt->execute();

            // Verificar se a exclusão foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Registro excluído com sucesso.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nenhum registro encontrado para excluir.']);
            }
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Método de requisição inválido.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Método de requisição inválido.'));
}
?>
