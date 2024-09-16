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
    $veiculo = $_POST['veiculo'];
    $motorista = $_POST['motorista'];
    $km_saida = $_POST['km_saida'];
    $km_chegada = !empty($_POST['km_chegada']) ? $_POST['km_chegada'] : null; // Trata como nulo se estiver vazio
    $horario_saida = $_POST['horario_saida'];
    $horario_chegada = $_POST['horario_chegada'];
    $destino = $_POST['destino'];
    $motivo = $_POST['motivo'];

// Validação simples do campo obrigatório
if (!isset($km_saida) || $km_saida === '') {
    http_response_code(400); // Código de erro de requisição inválida
    echo json_encode(array('message' => 'Campo KM saída é obrigatório.'));
    exit;
}

    // Prepara a consulta SQL para inserir os dados
    $sql = "INSERT INTO registros_veiculos (data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo)
            VALUES (:data, :porteiro, :veiculo, :motorista, :km_saida, :km_chegada, :horario_saida, :horario_chegada, :destino, :motivo)";

    // Prepara a instrução SQL para execução
    $stmt = $pdo->prepare($sql);

    // Executa a instrução SQL
    try {
        $stmt->execute(array(
            ':data' => $data,
            ':porteiro' => $porteiro,
            ':veiculo' => $veiculo,
            ':motorista' => $motorista,
            ':km_saida' => $km_saida,
            ':km_chegada' => $km_chegada,
            ':horario_saida' => $horario_saida,
            ':horario_chegada' => $horario_chegada,
            ':destino' => $destino,
            ':motivo' => $motivo,
        ));
        // Mensagem de sucesso
        echo json_encode(array('message' => 'Cadastro realizado com sucesso!'));
    } catch (PDOException $e) {
         // Em caso de erro, exibe a mensagem de erro
         echo json_encode(array('message' => 'Erro ao cadastrar veículo: ' . $e->getMessage()));
        }
    }


    
    