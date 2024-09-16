<<<<<<< HEAD
<?php
header('Content-Type: application/json');

// Configurações de conexão com o banco de dados
$host = '172.16.0.225';
$dbname = 'portaria';
$username = 'root';
$password = 'Meunome1@';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(array('status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()));
    exit;
}

// Função para validar dados
function validate_data($data) {
    return htmlspecialchars(strip_tags($data));
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            // Verifica o ID para exclusão
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

            if ($id > 0) {
                // Preparar a consulta SQL para excluir o registro
                $stmt = $pdo->prepare("DELETE FROM registro WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                try {
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['status' => 'success', 'message' => 'Registro excluído com sucesso.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Nenhum registro encontrado para excluir ou o registro já foi excluído.']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o registro: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            }
        } else {
            // Atualizar registro
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

                echo json_encode(array('status' => 'success', 'message' => 'Registro atualizado com sucesso!'));
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(array('status' => 'error', 'message' => 'Erro ao atualizar o registro: ' . $e->getMessage(), 'sql' => $sql));
            }
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Ação não especificada.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Método de requisição inválido.'));
}
?>
=======
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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(array('status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()));
    exit;
}

// Função para validar dados
function validate_data($data) {
    return htmlspecialchars(strip_tags($data));
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            // Verifica o ID para exclusão
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

            if ($id > 0) {
                // Preparar a consulta SQL para excluir o registro
                $stmt = $pdo->prepare("DELETE FROM registro WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                try {
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['status' => 'success', 'message' => 'Registro excluído com sucesso.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Nenhum registro encontrado para excluir ou o registro já foi excluído.']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o registro: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            }
        } else {
            // Atualizar registro
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

                echo json_encode(array('status' => 'success', 'message' => 'Registro atualizado com sucesso!'));
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(array('status' => 'error', 'message' => 'Erro ao atualizar o registro: ' . $e->getMessage(), 'sql' => $sql));
            }
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Ação não especificada.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Método de requisição inválido.'));
}
?>
>>>>>>> origin/master
