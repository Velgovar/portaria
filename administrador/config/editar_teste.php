<?php
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
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
    exit;
}

// Verifica se o método de solicitação é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitiza e valida as entradas
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
    $porteiro = filter_input(INPUT_POST, 'porteiro', FILTER_SANITIZE_STRING);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $tipovisitante = filter_input(INPUT_POST, 'tipovisitante', FILTER_SANITIZE_STRING);
    $servico = filter_input(INPUT_POST, 'servico', FILTER_SANITIZE_STRING);
    $empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
    $estacionamento = filter_input(INPUT_POST, 'estacionamento', FILTER_SANITIZE_STRING);
    $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_STRING);
    $horario_entrada = filter_input(INPUT_POST, 'horario_entrada', FILTER_SANITIZE_STRING);
    $horario_saida = filter_input(INPUT_POST, 'horario_saida', FILTER_SANITIZE_STRING);
    $colaborador = filter_input(INPUT_POST, 'colaborador', FILTER_SANITIZE_STRING);
    $setor = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_STRING);

    // Valida se o ID é um inteiro
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        echo 'ID inválido.';
        exit;
    }

    try {
        // Prepara a consulta SQL para atualizar a tabela registo
        $sql = "UPDATE registo SET 
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

        $stmt = $conn->prepare($sql);
        
        // Vincula os parâmetros
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':porteiro', $porteiro);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':tipovisitante', $tipovisitante);
        $stmt->bindParam(':servico', $servico);
        $stmt->bindParam(':empresa', $empresa);
        $stmt->bindParam(':estacionamento', $estacionamento);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':horario_entrada', $horario_entrada);
        $stmt->bindParam(':horario_saida', $horario_saida);
        $stmt->bindParam(':colaborador', $colaborador);
        $stmt->bindParam(':setor', $setor);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Executa a consulta
        if ($stmt->execute()) {
            echo 'Registro atualizado com sucesso.';
        } else {
            echo 'Erro ao atualizar registro.';
        }
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
    }
} else {
    echo 'Método de solicitação inválido.';
}
?>
