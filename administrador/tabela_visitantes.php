<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: logine.php');
    exit;
}
?>

<?php
// Configurações de conexão com o banco de dados
$host = '192.168.254.136';
$dbname = 'cobra';
$username = 'felipe';
$password = 'Aranhas12@';

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Definir o número de registros por página
    $registrosPorPagina = isset($_GET['registrosPorPagina']) ? (int)$_GET['registrosPorPagina'] : 10;

    // Capturar o número da página atual
    $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($paginaAtual - 1) * $registrosPorPagina;

    // Capturar os critérios de busca
    $criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'id';
    $busca = isset($_GET['busca']) ? $_GET['busca'] : '';

    // Validar o critério de busca
    $criteriosValidos = ['id', 'data', 'porteiro', 'nome', 'cpf', 'tipovisitante', 'servico', 'empresa', 'estacionamento', 'placa', 'horario_saida', 'horario_entrada', 'colaborador', 'setor'];
    if (!in_array($criterio, $criteriosValidos)) {
        $criterio = 'id';
    }

    // Consultar o total de registros com base no critério de busca
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registro WHERE $criterio LIKE :busca");
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $stmt->execute();
    $totalRegistros = $stmt->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    // Consultar os registros com base na página atual e critério de busca
    $stmt = $pdo->prepare("SELECT * FROM registro WHERE $criterio LIKE :busca ORDER BY id DESC LIMIT :offset, :limit");
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $registrosPorPagina, PDO::PARAM_INT);
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

// Verificar se há mensagem de sucesso
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Visitantes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/tabela_veiculos.css">
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: black;
    color: white;
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 95vh;
    background-size: cover;
    background-position: center;
}

.container_table1 {
    position: fixed;
    width: 95%; /* Aumenta a largura do container */
    max-width: 2000px; /* Ajusta o tamanho máximo do container */
    height: 90%;
    max-height: 85vh;
    padding: 20px;
    background-color: rgba(51, 51, 51, 0.9);
    border-radius: 4%;
    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                5px 5px 20px rgba(0, 0, 0, 0.2),
                10px 10px 25px rgba(0, 0, 0, 0.1);
}

.header {
    text-align: center;
    padding: 10px;
    color: white;
}

/* Ajusta o estilo da tabela para exibir mais linhas visíveis */
.table-container {
    height: calc(100% - 60px); /* Ajusta a altura para caber no container */
    overflow-y: auto;
    overflow-x: hidden; /* Remove a barra de rolagem horizontal */
    margin-top: 10px;
}

/* Ajusta a tabela e aplica zoom para aumentar o tamanho do texto e espaçamento */
table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #333;
    table-layout: fixed; /* Garante que as colunas tenham largura fixa */
    font-size: 0.9em; /* Reduzido o tamanho da fonte para 0.9em */
}

thead th {
    background-color: #222;
    color: #eee;
    position: sticky;
    top: 0;
    z-index: 2;
    word-wrap: break-word; /* Força a quebra de linha se necessário */
    white-space: normal; /* Permite a quebra de linha */
}

th, td {
    padding: 10px; /* Reduzido o padding para 10px */
    border: 2px solid #444;
    text-align: center;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal; /* Permite a quebra de linha em células */
    color: #ddd;
    font-size: 0.9em; /* Reduzido o tamanho da fonte para 0.9em */
}

tbody tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.1);
}

tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

th, td {
    min-width: 80px; /* Garante que as colunas não fiquem muito estreitas */
}

/* Opcional: Ajusta o estilo das colunas específicas */
th:nth-child(1), td:nth-child(1) { width: 30px; } 
th:nth-child(2), td:nth-child(2) { width: 56px; }
th:nth-child(3), td:nth-child(3) { width: 120px; }
th:nth-child(4), td:nth-child(4) { width: 120px; }
th:nth-child(5), td:nth-child(5) { width: 100px; }
th:nth-child(6), td:nth-child(6) { width: 60px; }
th:nth-child(7), td:nth-child(7) { width: 80px; }
th:nth-child(8), td:nth-child(8) { width: 80px; } 
th:nth-child(9), td:nth-child(9) { width: 60px; }
th:nth-child(10), td:nth-child(10) { width: 60px; }
th:nth-child(11), td:nth-child(11) { width: 70px; }
th:nth-child(12), td:nth-child(12) { width: 70px; }
th:nth-child(13), td:nth-child(13) { width: 100px; }
th:nth-child(14), td:nth-child(14) { width: 60px; }
th:nth-child(15), td:nth-child(15) { width: 120px; }




        /* Container da paginação */
        .pagination-container {
            position: absolute; /* Fixa o container de paginação na parte inferior da página */
            bottom: 3px; /* Ajusta a posição para estar um pouco acima da borda inferior da página */
            left: -20px; /* Ajusta a posição horizontal para a esquerda, movendo 40px para a esquerda (20px - 40px) */
            width: calc(100% - 60px); /* Ajusta a largura para acomodar o padding e o deslocamento */
            padding: 15px 0; /* Adiciona algum espaço acima e abaixo do conteúdo */
            z-index: 1000; /* Garante que a paginação fique acima do conteúdo */
            display: flex; /* Utiliza flexbox para alinhar o conteúdo */
            justify-content: space-between; /* Espaça os itens para a esquerda e direita */
            align-items: center; /* Alinha o conteúdo verticalmente */
        }

        /* Estilização da paginação */
        .pagination {
            display: flex; /* Utiliza flexbox para alinhar os botões */
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            border: 1px solid #444; /* Borda escura para combinar com o fundo */
            border-radius: 10px; /* Bordas arredondadas */
            color: #ddd; /* Texto claro */
            text-decoration: none;
            font-size: 14px;
            text-align: center;
        }

        .pagination a.active {
            background-color: #555; /* Cor do fundo da página ativa */
            color: #fff;
        }

        .pagination a.disabled {
            color: #666; /* Texto desativado */
            pointer-events: none; /* Desabilita o clique */
            cursor: not-allowed; /* Muda o cursor para indicar que não é clicável */
        }

        .pagination a:hover {
            background-color: #555; /* Cor de fundo ao passar o mouse */
        }


        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: rgba(51, 51, 51, 0.9);
    padding: 10px 20px;
    border-radius: 8px;
    width: 450px; /* Largura do modal ajustada */
    max-width: 90%;
    height: calc(85vh + 260px); /* Altura aumentada em 100px (40px anteriores + 100px adicionais) */
    box-sizing: border-box; /* Inclui padding e border na largura e altura total */
    overflow: hidden; /* Remove rolagem */
    box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.3),
                4px 4px 16px rgba(0, 0, 0, 0.2),
                8px 8px 20px rgba(0, 0, 0, 0.1);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transform: scale(0.8); /* Reduz o zoom do modal */
    transform-origin: center; /* Centraliza a transformação */
    margin-top: -5px; /* Ajuste a margem superior conforme necessário */
}


.modal-content h2 {
    margin-top: 0;
    color: white;
    font-size: 20px;
    text-align: center;
}

.modal-content label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: white;
}

.modal-content input {
    width: calc(100% - 20px);
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: #333;
    color: white;
    box-sizing: border-box;
}

.modal-content .input-group {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    width: 100%;
}

.modal-content .input-group div {
    flex: 1;
}

.modal-content button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    color: white;
    margin: 5px;
}

.modal-content button[type="submit"] {
    background-color: #4CAF50;
}

.modal-content button[type="submit"]:hover {
    background-color: #45a049;
}

/* Ocultar os campos que não devem aparecer no modal */
.modal-content .hidden {
    display: none;
}



.success-message {
    position: fixed;
    left: 50%;
    top: 831px;
    transform: translateX(-50%);
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px;
    border-radius: 5px;
    display: none;
    z-index: 1000;
}

.cancel-message {
    position: fixed;
    left: 50%;
    top: 831px;
    transform: translateX(-50%);
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 5px;
    display: none;
    z-index: 1000;
}

.edit-button {
    border: 0;
    color: white;
    line-height: 2;
    padding: 0 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.9rem;
    border-radius: 5px;
    cursor: pointer;
    background-color: rgb(100, 100, 100);
    background-image: linear-gradient(
        to top left,
        rgb(0 0 0 / 20%),
        rgb(0 0 0 / 20%) 30%,
        rgb(0 0 0 / 0%)
    );
    box-shadow:
        inset 2px 2px 3px rgb(255 255 255 / 60%),
        inset -2px -2px 3px rgb(0 0 0 / 60%);
}

.edit-button:hover {
    background-color: rgb(100, 100, 100);
    color: white;
    box-shadow:
        inset 2px 2px 3px rgb(0 0 0 / 60%),
        inset -2px -2px 3px rgb(255 255 255 / 60%);
    background-color: rgb(70, 70, 70);
}

.save-button {
    position: absolute;
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: none;
    font-size: 16px;
    z-index: 1000;
}

.save-button:hover {
    background-color: #45a049;
}

.cancel-button {
    position: absolute;
    background-color: #d30e00;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: none;
    font-size: 16px;
    z-index: 1000;
}

.cancel-button:hover {
    background-color: #b30000;
}

.search-container {
            margin-bottom: 20px;
        }
        /* Estilo do formulário de busca */
.search-form {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px; /* Espaçamento entre os campos */
    width: 80%;
    margin: 0 auto;
    padding: 15px;
    background-color: rgba(255, 255, 255, 0); /* Fundo transparente para o contêiner */
    border-radius: 10px;
}

/* Estilo dos rótulos */
.search-form label {
    font-size: 16px;
    color: #ffffff; /* Cor do texto em branco */
    font-weight: bold;
    margin-right: 10px;
}

/* Estilo do seletor de critérios */
.search-form select {
    padding: 8px;
    font-size: 14px;
    border: 1px solid #b0b0b0;
    border-radius: 5px;
    background-color: rgba(51, 51, 51, 0.9); /* Fundo escuro com 90% de opacidade */
    color: #ffffff; /* Texto em branco */
    width: 120px; /* Ajustado para largura menor */
}

/* Estilo do campo de busca */
.search-form .input-container {
    position: relative; /* Para posicionar o ícone de busca dentro do campo */
    width: 200px; /* Largura ajustada do campo de busca */
}

.search-form input[type="text"] {
    padding: 8px 40px 8px 10px; /* Aumentado o espaço à direita para o ícone */
    font-size: 14px;
    border: 1px solid #b0b0b0;
    border-radius: 5px;
    background-color: rgba(51, 51, 51, 0.9); /* Fundo escuro com 90% de opacidade */
    color: #ffffff; /* Texto em branco */
    width: 100%; /* Ocupa toda a largura do contêiner */
}

/* Estilo do botão de busca */
.search-form button[type="submit"] {
    position: absolute;
    right: -45px; /* Move a lupa 20px para a direita */
    top: 50%; /* Centralizado verticalmente */
    transform: translateY(-50%); /* Ajusta o ícone para o centro vertical */
    background: none; /* Remove o fundo do botão */
    border: none; /* Remove a borda do botão */
    cursor: pointer; /* Cursor de ponteiro ao passar sobre o ícone */
}

/* Estilo do ícone de busca */
.search-form .search-icon {
    font-size: 16px;
    color: #ffffff; /* Cor do ícone */
}

/* Estilos para o botão de cancelar */
#cancelButton {
    background-color: #f44336; /* Cor vermelha para o botão de cancelar */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    cursor: pointer;
    margin-left: 10px;
}

#cancelButton:hover {
    background-color: #d32f2f; /* Cor vermelha mais escura ao passar o mouse */
}

/* Estilo para o contêiner do launcher */
.launcher {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999; /* Garante que esteja acima de outros elementos */
    background-color: #4CAF50; /* Verde para sucesso */
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    opacity: 1;
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
    font-family: Arial, sans-serif;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Estilo para a mensagem dentro do launcher */
.launcher-message {
    margin: 0;
}

/* Animação para o launcher aparecer */
.launcher-show {
    transform: translateY(0);
}

/* Animação para o launcher desaparecer */
.hidden {
    opacity: 0;
    transform: translateY(-20px);
}

/* Estilos para o launcher de exclusão */
.launcher-delete {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #f44336; /* Cor de fundo vermelho para o launcher de exclusão */
    color: #fff; /* Cor do texto branco */
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: none; /* Oculto por padrão */
    z-index: 1000; /* Garantir que esteja acima de outros elementos */
    text-align: center;
    font-family: Arial, sans-serif;
}

.launcher-delete-message {
    font-size: 16px;
}

.hidden {
    display: none;
}

.visible {
    display: block;
}

/* Botão Excluir */
.delete-button {
    border: 0;
    color: white;
    line-height: 2; /* Reduz a altura da linha para tornar o botão mais fino */
    padding: 0 7px; /* Reduz o padding horizontal para diminuir a largura */
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.9rem; /* Ajuste o tamanho da fonte se necessário */
    border-radius: 5px; /* Mantém o border-radius para bordas arredondadas */
    cursor: pointer;
    background-color: rgb(150, 0, 0); /* Cor vermelha escura */
    background-image: linear-gradient(
        to top left,
        rgb(0 0 0 / 20%),
        rgb(0 0 0 / 20%) 30%,
        rgb(0 0 0 / 0%)
    );
    box-shadow:
        inset 2px 2px 3px rgb(255 255 255 / 60%),
        inset -2px -2px 3px rgb(0 0 0 / 60%);
}

.delete-button:hover {
    background-color: rgb(120, 0, 0); /* Cor de fundo ao passar o mouse */
}

.delete-button:active {
    box-shadow:
        inset -2px -2px 3px rgb(255 255 255 / 60%),
        inset 2px 2px 3px rgb(0 0 0 / 60%);
}

    </style>
</head>
<body>
<a href="menu.php" class="style">Voltar</a>

    <div class="container_table1">
        <div class="header">
            <h2>[Administrador] Cadastros de Visitantes</h2>
        </div>

        <!-- Formulário de Busca -->
        <form class="search-form" method="get" action="">
            <label for="campo-busca">Buscar por</label>
            <select name="criterio" id="criterio">
                <option value="id" <?php echo $criterio == 'id' ? 'selected' : ''; ?>>ID</option>
                <option value="data" <?php echo $criterio == 'data' ? 'selected' : ''; ?>>DATA</option>
                <option value="porteiro" <?php echo $criterio == 'porteiro' ? 'selected' : ''; ?>>PORTEIRO</option>
                <option value="nome" <?php echo $criterio == 'nome' ? 'selected' : ''; ?>>NOME</option>
                <option value="cpf" <?php echo $criterio == 'cpf' ? 'selected' : ''; ?>>CPF</option>
                <option value="tipovisitante" <?php echo $criterio == 'tipovisitante' ? 'selected' : ''; ?>>TIPO DE VISITANTE</option>
                <option value="servico" <?php echo $criterio == 'servico' ? 'selected' : ''; ?>>SERVIÇO</option>
                <option value="empresa" <?php echo $criterio == 'empresa' ? 'selected' : ''; ?>>EMPRESA</option>
                <option value="estacionamento" <?php echo $criterio == 'estacionamento' ? 'selected' : ''; ?>>ESTACIONAMENTO</option>
                <option value="placa" <?php echo $criterio == 'placa' ? 'selected' : ''; ?>>PLACA</option>
                <option value="horario_entrada" <?php echo $criterio == 'horario_entrada' ? 'selected' : ''; ?>>HORÁRIO ENTRADA</option>
                <option value="horario_saida" <?php echo $criterio == 'horario_saida' ? 'selected' : ''; ?>>HORÁRIO SAÍDA</option>
                <option value="colaborador" <?php echo $criterio == 'colaborador' ? 'selected' : ''; ?>>COLABORADOR</option>
                <option value="setor" <?php echo $criterio == 'setor' ? 'selected' : ''; ?>>SETOR</option>
            </select>
            <div class="input-container">
                    <input type="text" name="busca" id="campo-busca" placeholder="Digite sua busca">
                    <button type="submit">
                        <i class="fas fa-search search-icon"></i>
                    </button>
                
            </div>
        </form>

        <!-- Mensagem de Sucesso -->
        <?php if ($mensagemSucesso): ?>
            <div class="success-message"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
        <?php endif; ?>

<!-- Tabela de Registros -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Porteiro</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Tipo Visitante</th>
                <th>Serviço</th>
                <th>Empresa</th>
                <th>Estacionamento</th>
                <th>Placa</th>
                <th>Horário Entrada</th>
                <th>Horário Saída</th>
                <th>Colaborador</th>
                <th>Setor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $registro): ?>
                <tr data-id="<?php echo htmlspecialchars($registro['id']); ?>">
            <td class="id"><?php echo htmlspecialchars($registro['id']); ?></td>
            <td class="data"><?php echo htmlspecialchars($registro['data']); ?></td>
            <td class="porteiro"><?php echo htmlspecialchars($registro['porteiro']); ?></td>
            <td class="nome"><?php echo htmlspecialchars($registro['nome']); ?></td>
            <td class="cpf"><?php echo htmlspecialchars($registro['cpf']); ?></td>
            <td class="tipovisitante"><?php echo htmlspecialchars($registro['tipovisitante']); ?></td>
            <td class="servico"><?php echo htmlspecialchars($registro['servico']); ?></td>
            <td class="empresa"><?php echo htmlspecialchars($registro['empresa']); ?></td>
            <td class="estacionamento"><?php echo htmlspecialchars($registro['estacionamento']); ?></td>
            <td><?php echo htmlspecialchars(!empty($registro['placa']) ? $registro['placa'] : '-'); ?></td>
            <td class="horario_entrada"><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_entrada']))); ?></td>
<td class="horario_saida"><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_saida']))); ?></td>

            <td class="colaborador"><?php echo htmlspecialchars($registro['colaborador']); ?></td>
            <td class="setor"><?php echo htmlspecialchars($registro['setor']); ?></td>
                    
                    
            <td>
    <button class="edit-button" 
        data-id="<?php echo htmlspecialchars($registro['id']); ?>" 
        data-data="<?php echo htmlspecialchars($registro['data']); ?>"
        data-porteiro="<?php echo htmlspecialchars($registro['porteiro']); ?>"
        data-nome="<?php echo htmlspecialchars($registro['nome']); ?>"
        data-cpf="<?php echo htmlspecialchars($registro['cpf']); ?>"
        data-tipovisitante="<?php echo htmlspecialchars($registro['tipovisitante']); ?>"
        data-servico="<?php echo htmlspecialchars($registro['servico']); ?>"
        data-empresa="<?php echo htmlspecialchars($registro['empresa']); ?>"
        data-estacionamento="<?php echo htmlspecialchars($registro['estacionamento']); ?>"
        data-placa="<?php echo htmlspecialchars($registro['placa']); ?>"
        data-horario_entrada="<?php echo htmlspecialchars($registro['horario_entrada']); ?>"
        data-horario_saida="<?php echo htmlspecialchars($registro['horario_saida']); ?>"
        data-colaborador="<?php echo htmlspecialchars($registro['colaborador']); ?>"
        data-setor="<?php echo htmlspecialchars($registro['setor']); ?>">
        Editar
    </button>
    <button class="delete-button" 
        data-id="<?php echo htmlspecialchars($registro['id']); ?>"
        onclick="openConfirmationModal(<?php echo htmlspecialchars($registro['id']); ?>)">
        Excluir
    </button>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


        <div class="pagination-container">
                    <div class="select-container">
                        <select id="registrosPorPagina" onchange="changeRecordsPerPage()">
                            <option value="10" <?php echo $registrosPorPagina == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $registrosPorPagina == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $registrosPorPagina == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $registrosPorPagina == 100 ? 'selected' : ''; ?>>100</option>
                            <option value="1000" <?php echo $registrosPorPagina == 1000 ? 'selected' : ''; ?>>1000</option>
                            <option value="10000" <?php echo $registrosPorPagina == 10000 ? 'selected' : ''; ?>>10000</option>
                            <option value="100000" <?php echo $registrosPorPagina == 100000 ? 'selected' : ''; ?>>100000</option>
                            <option value="1000000" <?php echo $registrosPorPagina == 1000000 ? 'selected' : ''; ?>>1000000</option>
                        </select>
                        <span>Linhas / Páginas</span>
                    </div>

                    <div class="pagination">
                        <a href="?pagina=1&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == 1) echo 'class="disabled"'; ?>>&laquo;</a>
                        <a href="?pagina=<?php echo max(1, $paginaAtual - 1); ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == 1) echo 'class="disabled"'; ?>>&lt;</a>
                        
                        <?php
                        $numLinks = 3;
                        $startPage = max(1, $paginaAtual - floor($numLinks / 2));
                        $endPage = min($totalPaginas, $startPage + $numLinks - 1);
                        
                        if ($endPage - $startPage + 1 < $numLinks) {
                            $startPage = max(1, $endPage - $numLinks + 1);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="?pagina=<?php echo $i; ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($i == $paginaAtual) echo 'class="active"'; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <a href="?pagina=<?php echo min($totalPaginas, $paginaAtual + 1); ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == $totalPaginas) echo 'class="disabled"'; ?>>&gt;</a>
                        <a href="?pagina=<?php echo $totalPaginas; ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == $totalPaginas) echo 'class="disabled"'; ?>>&raquo;</a>
                    </div>
                </div>
    </div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h2>Editar Registro</h2>
        <form id="editForm" method="post" action="../config/visitantes.php">
            <input type="hidden" name="id" id="editId">
            
            <label for="editData">Data</label>
            <input type="date" name="data" id="editData" required>

            <label for="editPorteiro">Porteiro</label>
            <input type="text" name="porteiro" id="editPorteiro" required>

            <label for="editNome">Nome</label>
            <input type="text" name="nome" id="editNome" required>

            <label for="editCpf">CPF</label>
            <input type="text" name="cpf" id="editCpf" required>

            <label for="editTipoVisitante">Tipo Visitante</label>
            <input type="text" name="tipovisitante" id="editTipoVisitante" required>

            <label for="editServico">Serviço</label>
            <input type="text" name="servico" id="editServico" required>

            <label for="editEmpresa">Empresa</label>
            <input type="text" name="empresa" id="editEmpresa" required>

            <label for="editEstacionamento">Estacionamento</label>
            <input type="text" name="estacionamento" id="editEstacionamento" required>

            <label for="editPlaca">Placa</label>
            <input type="text" name="placa" id="editPlaca" required>

            <div class="input-group">
                <div>
                    <label for="editHorarioEntrada">Horário de Entrada</label>
                    <input type="time" name="horario_entrada" id="editHorarioEntrada" required>
                </div>
                <div>
                    <label for="editHorarioSaida">Horário de Saída</label>
                    <input type="time" name="horario_saida" id="editHorarioSaida" required>
                </div>
            </div>

            <label for="editColaborador">Colaborador</label>
            <input type="text" name="colaborador" id="editColaborador" required>

            <label for="editSetor">Setor</label>
            <input type="text" name="setor" id="editSetor" required>

            <button type="submit">Salvar</button>
            <button type="button" id="cancelButton">Cancelar</button>
        </form>
    </div>
</div>

<!-- Contêiner para a notificação -->
<div id="launcher" class="launcher hidden">
    <div class="launcher-message">Cadastro editado com sucesso!</div>
</div>

<!-- Modal de confirmação -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <h3>Confirmação de Exclusão</h3>
        <p>Você tem certeza que deseja excluir este cadastro?</p>
        <form id="confirmationForm">
<!-- Campo de texto para confirmação -->
<input type="text" id="confirmationInput" autocomplete="off" placeholder="Digite 'excluir' para confirmar">
            <button type="submit">Excluir</button>
            <button type="button" class="cancel" onclick="closeConfirmationModal()">Cancelar</button>
        </form>
    </div>
</div>

<div class="message-container"></div>



    <script>
// Abrir o modal de edição
document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const data = this.getAttribute('data-data');
        const porteiro = this.getAttribute('data-porteiro');
        const nome = this.getAttribute('data-nome');
        const cpf = this.getAttribute('data-cpf');
        const tipoVisitante = this.getAttribute('data-tipovisitante');
        const servico = this.getAttribute('data-servico');
        const empresa = this.getAttribute('data-empresa');
        const estacionamento = this.getAttribute('data-estacionamento');
        const placa = this.getAttribute('data-placa');
        const horarioEntrada = this.getAttribute('data-horario_entrada');
        const horarioSaida = this.getAttribute('data-horario_saida');
        const colaborador = this.getAttribute('data-colaborador');
        const setor = this.getAttribute('data-setor');

        // Preencher os campos do modal com os dados do registro
        document.getElementById('editId').value = id;
        document.getElementById('editData').value = data || '';
        document.getElementById('editPorteiro').value = porteiro || '';
        document.getElementById('editNome').value = nome || '';
        document.getElementById('editCpf').value = cpf || '';
        document.getElementById('editTipoVisitante').value = tipoVisitante || '';
        document.getElementById('editServico').value = servico || '';
        document.getElementById('editEmpresa').value = empresa || '';
        document.getElementById('editEstacionamento').value = estacionamento || '';
        document.getElementById('editPlaca').value = placa || '';
        document.getElementById('editHorarioEntrada').value = horarioEntrada || '';
        document.getElementById('editHorarioSaida').value = horarioSaida || '';
        document.getElementById('editColaborador').value = colaborador || '';
        document.getElementById('editSetor').value = setor || '';

        // Abrir o modal
        document.getElementById('editModal').style.display = 'flex';
    });
});

// Fechar o modal ao clicar no botão "Cancelar"
document.getElementById('cancelButton').addEventListener('click', function() {
    closeModalFunction();
});

// Fechar o modal ao clicar fora dele
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('editModal')) {
        closeModalFunction();
    }
});

// Função para fechar o modal e resetar os campos
function closeModalFunction() {
    document.getElementById('editModal').style.display = 'none';
    resetModalFields();
}

// Função para resetar os campos do modal
function resetModalFields() {
    document.getElementById('editForm').reset();
}

// Enviar a atualização via AJAX
// Lidar com o envio do formulário de edição
document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    const formData = new FormData(this);

    fetch('config/tabela_visitantes_config.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta do servidor:', data); // Verifique a resposta

        // Ocultar a notificação padrão (em alguns navegadores, isso não pode ser feito diretamente, mas vamos garantir que só a nossa notificação seja exibida)
        const launcher = document.getElementById('launcher');
        if (data.message.includes('sucesso')) {
            // Mostrar a notificação personalizada
            launcher.classList.remove('hidden');
            launcher.classList.add('visible');

            // Remover a notificação personalizada após 2 segundos
            setTimeout(() => {
                launcher.classList.remove('visible');
                launcher.classList.add('hidden');
            }, 2000);

            // Fechar o modal e atualizar a linha da tabela
            closeModalFunction();
            updateTableRow(
                formData.get('id'),
                formData.get('data'),
                formData.get('porteiro'),
                formData.get('nome'),
                formData.get('cpf'),
                formData.get('tipovisitante'),
                formData.get('servico'),
                formData.get('empresa'),
                formData.get('estacionamento'),
                formData.get('placa'),
                formData.get('horario_entrada'),
                formData.get('horario_saida'),
                formData.get('colaborador'),
                formData.get('setor')
            );
        } else {
            console.error('Falha ao editar o registro:', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao enviar a atualização.');
    });
});

// Função para atualizar a linha da tabela com os novos dados
function updateTableRow(id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horarioEntrada, horarioSaida, colaborador, setor) {
    if (!id) {
        console.error('ID não fornecido.');
        return;
    }

    // Selecionar a linha da tabela com o ID fornecido
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        console.log(`Atualizando a linha com ID ${id}`); // Log para depuração

        // Atualizar o conteúdo das células
        const dataCell = row.querySelector('.data');
        const porteiroCell = row.querySelector('.porteiro');
        const nomeCell = row.querySelector('.nome');
        const cpfCell = row.querySelector('.cpf');
        const tipovisitanteCell = row.querySelector('.tipovisitante');
        const servicoCell = row.querySelector('.servico');
        const empresaCell = row.querySelector('.empresa');
        const estacionamentoCell = row.querySelector('.estacionamento');
        const placaCell = row.querySelector('.placa');
        const horarioEntradaCell = row.querySelector('.horario_entrada');
        const horarioSaidaCell = row.querySelector('.horario_saida');
        const colaboradorCell = row.querySelector('.colaborador');
        const setorCell = row.querySelector('.setor');

        if (dataCell) dataCell.textContent = data;
        if (porteiroCell) porteiroCell.textContent = porteiro;
        if (nomeCell) nomeCell.textContent = nome;
        if (cpfCell) cpfCell.textContent = cpf;
        if (tipovisitanteCell) tipovisitanteCell.textContent = tipovisitante;
        if (servicoCell) servicoCell.textContent = servico;
        if (empresaCell) empresaCell.textContent = empresa;
        if (estacionamentoCell) estacionamentoCell.textContent = estacionamento;
        if (placaCell) placaCell.textContent = placa;
        if (horarioEntradaCell) horarioEntradaCell.textContent = horarioEntrada;
        if (horarioSaidaCell) horarioSaidaCell.textContent = horarioSaida;
        if (colaboradorCell) colaboradorCell.textContent = colaborador;
        if (setorCell) setorCell.textContent = setor;

        console.log('Linha atualizada com sucesso.');
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}


// Variável global para armazenar o ID do item a ser excluído
let deleteId = null;

// Abrir o modal de confirmação de exclusão
document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', function() {
        deleteId = this.getAttribute('data-id'); // Define o ID do item a ser excluído
        document.getElementById('confirmationModal').style.display = 'flex'; // Abre o modal
    });
});

// Função para fechar o modal de confirmação
function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('confirmationInput').value = ''; // Limpa o campo de texto
}

// Função para confirmar a exclusão
document.getElementById('confirmationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    const confirmationInput = document.getElementById('confirmationInput').value.trim().toLowerCase();
    if (confirmationInput === 'excluir') {
        fetch('config/tabela_visitantes_config.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': deleteId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resposta do servidor:', data);

            if (data.status === 'success') {
                // Remove a linha da tabela
                const row = document.querySelector(`tr[data-id="${deleteId}"]`);
                if (row) {
                    row.remove();
                }

                // Mostra a mensagem de sucesso
                showMessage('Registro excluído com sucesso!', 'success');
            } else {
                // Mostra a mensagem de erro
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao excluir o registro.', 'error');
        });

        closeConfirmationModal(); // Fecha o modal após a confirmação
    } else {
        showMessage('Você deve digitar "excluir" para confirmar.', 'warning');
    }
});

// Função para mostrar mensagens
function showMessage(message, type) {
    const messageContainer = document.querySelector('.message-container');
    messageContainer.textContent = message;
    messageContainer.className = `message-container ${type}`; // Adiciona a classe de tipo (success, error, warning)
    setTimeout(() => {
        messageContainer.textContent = '';
    }, 2000);
}

// Fechar o modal de confirmação ao clicar fora dele
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('confirmationModal')) {
        closeConfirmationModal();
    }
});


// Função para alterar o número de registros por página
function changeRecordsPerPage() {
    const select = document.getElementById('registrosPorPagina');
    const registrosPorPagina = select.value;
    window.location.href = `?pagina=1&registrosPorPagina=${registrosPorPagina}`;
}

// Adicionar event listener para mudança no número de registros por página
document.getElementById('registrosPorPagina').addEventListener('change', changeRecordsPerPage);



    </script>
</body>
</html>