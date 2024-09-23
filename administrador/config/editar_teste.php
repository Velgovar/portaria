<?php
header('Content-Type: application/json');

// Configurações de conexão com o banco de dados
$servername = "172.16.0.225";
$username = "root";
$password = "Meunome1@";
$dbname = "portaria";

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    // Corrigindo para usar $servername em vez de $host
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(array('message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()));
    exit;
}

// Função para validar dados
function validate_data($data) {
    return htmlspecialchars(strip_tags($data));
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe e valida os dados do formulário
    $id = isset($_POST['id']) ? validate_data($_POST['id']) : null;
    $data = isset($_POST['data']) ? validate_data($_POST['data']) : null;
    $porteiro = isset($_POST['porteiro']) ? validate_data($_POST['porteiro']) : null;
    $nome = isset($_POST['nome']) ? validate_data($_POST['nome']) : null;
    $cpf = isset($_POST['cpf']) ? validate_data($_POST['cpf']) : null;
    $tipovisitante = isset($_POST['tipovisitante']) ? validate_data($_POST['tipovisitante']) : null;
    $servico = isset($_POST['servico']) ? validate_data($_POST['servico']) : null;
    $empresa = isset($_POST['empresa']) ? validate_data($_POST['empresa']) : null;
    $estacionamento = isset($_POST['estacionamento']) ? validate_data($_POST['estacionamento']) : null;
    $placa = isset($_POST['placa']) ? validate_data($_POST['placa']) : null;
    $horarioEntrada = isset($_POST['horario_entrada']) ? validate_data($_POST['horario_entrada']) : null;
    $horarioSaida = isset($_POST['horario_saida']) ? validate_data($_POST['horario_saida']) : null;
    $colaborador = isset($_POST['colaborador']) ? validate_data($_POST['colaborador']) : null;
    $setor = isset($_POST['setor']) ? validate_data($_POST['setor']) : null;

    // Prepara a consulta SQL para atualizar os dados
    $sql = "UPDATE registro SET 
                data = :data, 
                porteiro = :porteiro, 
                nome = :nome, 
                cpf = :cpf, 
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
            ':cpf' => $cpf,
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

        echo json_encode(array('message' => 'Registro atualizado com sucesso!'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('message' => 'Erro ao atualizar o registro: ' . $e->getMessage(), 'sql' => $sql));
    }
} else {
    echo json_encode(array('message' => 'Método de requisição inválido.'));
}
?>
