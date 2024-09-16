<?php
// Configurações do banco de dados
$host = '172.16.0.225'; // Host do banco de dados
$dbname = 'portaria'; // Nome do banco de dados
$username = 'root'; // Nome de usuário do banco de dados
$password = 'Meunome1@'; // Senha do banco de dados

// Configuração do charset para conexão
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

// Tentativa de conexão com o banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Modo de erros para Exception
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $data = $_POST['data'];
    $porteiro = $_POST['porteiro'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $tipovisitante = $_POST['tipovisitante'];
    $servico = $_POST['servico'];
    $empresa = $_POST['empresa'];
    $estacionamento = $_POST['estacionamento'];
    $placa = $_POST['placa'];
    $horario_entrada = $_POST['horario_entrada'];
    $horario_saida = !empty($_POST['horario_saida']) ? $_POST['horario_saida'] : null; // Definindo como NULL se estiver vazio
    $colaborador = $_POST['colaborador'];
    $setor = $_POST['setor'];

    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO registro (data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor)
            VALUES (:data, :porteiro, :nome, :cpf, :tipovisitante, :servico, :empresa, :estacionamento, :placa, :horario_entrada, :horario_saida, :colaborador, :setor)";

    // Prepara a instrução SQL para execução
    $stmt = $pdo->prepare($sql);

    // Executa a instrução SQL
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
            ':horario_entrada' => $horario_entrada,
            ':horario_saida' => $horario_saida,
            ':colaborador' => $colaborador,
            ':setor' => $setor,
        ));
        // Mensagem de sucesso
        echo json_encode(array('message' => 'Cadastro realizado com sucesso!'));
    } catch (PDOException $e) {
        // Em caso de erro, exibe a mensagem de erro
        http_response_code(500); // Código de erro interno do servidor
        echo json_encode(array('message' => 'Erro ao cadastrar visitante: ' . $e->getMessage()));
    }
}
