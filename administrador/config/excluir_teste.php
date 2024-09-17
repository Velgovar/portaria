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

// Verifica se o ID foi enviado para exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepara a consulta para deletar o registro com o ID fornecido
    $sql = "DELETE FROM registro WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        // Executa a instrução SQL com o ID fornecido
        $stmt->execute([':id' => $id]);

        // Verifica se alguma linha foi afetada (ou seja, se o registro foi encontrado e excluído)
        if ($stmt->rowCount() > 0) {
            echo json_encode(array('message' => 'Registro excluído com sucesso!'));
        } else {
            echo json_encode(array('message' => 'Registro não encontrado.'));
        }
    } catch (PDOException $e) {
        http_response_code(500); // Código de erro interno do servidor
        echo json_encode(array('message' => 'Erro ao excluir o registro: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('message' => 'ID não fornecido.'));
}
?>
