<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: logine.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Registros</title>
    <link rel="stylesheet" href="../css/cadastros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        /* Estilos para o botão de voltar */
           .style {
            position: absolute;
            top: 10px;
            left: 10px;
            border: 0;
            color: white;
            line-height: 2.5;
            padding: 0 70px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            background-color: rgb(0, 138, 143);
            background-image: linear-gradient(
              to top left,
                rgb(0 0 0 / 20%),
                rgb(0 0 0 / 20%) 30%,
                rgb(0 0 0 / 0%));
             box-shadow:
            inset 2px 2px 3px rgb(255 255 255 / 60%),
              inset -2px -2px 3px rgb(0 0 0 / 60%);
            }
            .style:hover {
             background-color: rgb(0, 209, 216);
            }
            .style:active {
             box-shadow:
            inset -2px -2px 3px rgb(255 255 255 / 60%),
            inset 2px 2px 3px rgb(0 0 0 / 60%);
            }        
    </style>
</head>
<body>
<a href="../login/entrar.php" class="style">Voltar</a>

    <div class="container">
        <h2>Registros de Visitantes</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Porteiro</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Tipo de Visitante</th>
                    <th>Serviço</th>
                    <th>Empresa</th>
                    <th>Estacionamento</th>
                    <th>Placa</th>
                    <th>Horário de Entrada</th>
                    <th>Horário de Saída</th>
                    <th>Colaborador</th>
                    <th>Setor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Configurações do banco de dados
                $host = '192.168.254.136';
                $dbname = 'cobra';
                $username = 'felipe';
                $password = 'Aranhas12@';

                // Configuração do charset para conexão
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                );

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
                }

                // Consulta para buscar todos os registros
                $sql = "SELECT * FROM registro";
                $stmt = $pdo->query($sql);

                // Exibe os registros na tabela
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['data']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['porteiro']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['cpf']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['tipovisitante']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['servico']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['empresa']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['estacionamento']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['placa']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['horario_entrada']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['horario_saida']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['colaborador']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['setor']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
