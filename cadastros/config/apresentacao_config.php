<?php
header('Content-Type: application/json');

// Inclui as configurações do banco de dados
require '../../db_config.php'; // Verifique se o caminho está correto

// Configuração do charset para conexão
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

// Tentativa de conexão com o banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Modo de erros para Exception
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $data = $_POST['data'];
    $porteiro = $_POST['porteiro'];
    $horario_apresentacao = $_POST['horario_apresentacao'];
    $nome = $_POST['nome'];
    $empresa = $_POST['empresa'];


    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO apresentacao (data, porteiro, horario_apresentacao, nome, empresa)
            VALUES (:data, :porteiro, :horario_apresentacao, :nome, :empresa)";

    // Prepara a instrução SQL para execução
    $stmt = $pdo->prepare($sql);

    // Definindo os parâmetros a serem enviados
    $params = array(
        ':data' => $data,
        ':porteiro' => $porteiro,
        ':horario_apresentacao' => $horario_apresentacao,
        ':nome' => $nome,
        ':empresa' => $empresa,

    );

    // Executa a instrução SQL
    try {
        $stmt->execute($params);
        // Mensagem de sucesso
        echo json_encode(array('message' => 'Cadastro realizado com sucesso!'));
    } catch (PDOException $e) {
        // Em caso de erro, exibe a mensagem de erro
        http_response_code(500);
        echo json_encode(array('message' => 'Erro ao cadastrar veículo: ' . $e->getMessage()));
    }
}
?>
