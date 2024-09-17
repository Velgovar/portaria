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
    echo "Conexão realizada com sucesso!"; // Debug de conexão
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificação de recebimento de dados
    if (!empty($_POST)) {
        echo "Dados recebidos: ";
        var_dump($_POST); // Exibe os dados recebidos para debug
    }

    // Recebe os dados do formulário
    $data = $_POST['data'] ?? null;
    $porteiro = $_POST['porteiro'] ?? null;
    $nome = $_POST['nome'] ?? null;
    $cpf = $_POST['cpf'] ?? null; // Utilizando CPF como identificador único
    $tipovisitante = $_POST['tipovisitante'] ?? null;
    $servico = $_POST['servico'] ?? null;
    $empresa = $_POST['empresa'] ?? null;
    $estacionamento = $_POST['estacionamento'] ?? null;
    $placa = $_POST['placa'] ?? null;
    $horario_entrada = $_POST['horario_entrada'] ?? null;
    $horario_saida = !empty($_POST['horario_saida']) ? $_POST['horario_saida'] : null;
    $colaborador = $_POST['colaborador'] ?? null;
    $setor = $_POST['setor'] ?? null;

    // Verifica se o CPF foi enviado
    if (empty($cpf)) {
        echo "Erro: CPF não fornecido.";
        exit;
    }

    // Primeiro, verificamos se já existe um registro com o mesmo CPF
    $sql_check = "SELECT COUNT(*) FROM registro WHERE cpf = :cpf";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':cpf' => $cpf]);

    if ($stmt_check->fetchColumn() > 0) {
        // Se o registro já existe, fazemos uma atualização
        $sql = "UPDATE registro SET 
                    data = :data, 
                    porteiro = :porteiro, 
                    nome = :nome, 
                    tipovisitante = :tipovisitante, 
                    servico = :servico, 
                    empresa = :empresa, 
                    estacionamento = :estacionamento, 
                    placa = :placa, 
                    horario_entrada = :horario_entrada, 
                    horario_saida = :horario_saida, 
                    colaborador = :colaborador, 
                    setor = :setor
                WHERE cpf = :cpf";

        $stmt = $pdo->prepare($sql);

        try {
            // Executa a atualização
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
            echo json_encode(array('message' => 'Registro atualizado com sucesso!'));
        } catch (PDOException $e) {
            http_response_code(500); // Código de erro interno do servidor
            echo json_encode(array('message' => 'Erro ao atualizar o registro: ' . $e->getMessage()));
        }
    } else {
        // Se o registro não existe, fazemos a inserção
        $sql = "INSERT INTO registro (data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor)
                VALUES (:data, :porteiro, :nome, :cpf, :tipovisitante, :servico, :empresa, :estacionamento, :placa, :horario_entrada, :horario_saida, :colaborador, :setor)";

        $stmt = $pdo->prepare($sql);

        try {
            // Executa a inserção
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
            echo json_encode(array('message' => 'Cadastro realizado com sucesso!'));
        } catch (PDOException $e) {
            http_response_code(500); // Código de erro interno do servidor
            echo json_encode(array('message' => 'Erro ao cadastrar visitante: ' . $e->getMessage()));
        }
    }
} else {
    echo "Nenhum dado foi enviado!";
}
?>
