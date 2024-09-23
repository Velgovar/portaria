<?php
require '../db_config.php'; // Ajuste o caminho conforme a localização do seu arquivo

try {
    // Conectar ao banco de dados usando as variáveis de configuração
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

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

// Verificar se há mensagem de sucesso
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Veículos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/tabela_visitantes.css">

</head>
<body>
    <a href="../menu.html" class="voltar">Voltar</a>
    <img src="../images/inusitta.png" class="image-center" alt="icone central">

    <div class="container_table1">
        <div class="header">
        <h2>Tabela Visitantes</h2>
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
                    <td><?php echo htmlspecialchars($registro['id']); ?></td>
                    <td><?php echo htmlspecialchars($registro['data']); ?></td>
                    <td><?php echo htmlspecialchars($registro['porteiro']); ?></td>
                    <td><?php echo htmlspecialchars($registro['nome']); ?></td>
                    <td><?php echo htmlspecialchars($registro['cpf']); ?></td>
                    <td><?php echo htmlspecialchars($registro['tipovisitante']); ?></td>
                    <td><?php echo htmlspecialchars($registro['servico']); ?></td>
                    <td><?php echo htmlspecialchars($registro['empresa']); ?></td>
                    <td><?php echo htmlspecialchars($registro['estacionamento']); ?></td>
                    <td><?php echo htmlspecialchars(!empty($registro['placa']) ? $registro['placa'] : '-'); ?></td>
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_entrada']))); ?></td>
<td class="horario_saida"><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_saida']))); ?></td>

                    <td><?php echo htmlspecialchars($registro['colaborador']); ?></td>
                    <td><?php echo htmlspecialchars($registro['setor']); ?></td>
                    <td>
                        <button class="edit-button" 
                            data-id="<?php echo htmlspecialchars($registro['id']); ?>" 
                            data-horario_saida="<?php echo htmlspecialchars($registro['horario_saida']); ?>">
                            Editar
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
        <span class="close" id="closeModal"></span>
        <h2>Editar Registro</h2>
        <form id="editForm" method="post" action="../config/editar_registro_visitantes.php">
            <input type="hidden" name="id" id="editId">
            <div class="input-icon">

            <label for="editHorarioSaida">Horário de Saída</label>
            <input type="text" name="horario_saida" id="editHorarioSaida" class="flatpickr" required>
            <i class="fas fa-clock"></i> 
                        </div>
            <button type="submit" class="salvar">Salvar</button>
            <button type="button" class="cancelar" id="cancelButton">Cancelar</button>
        </form>
    </div>
</div>

<div class="message-container"></div>

<!-- Contêiner para o launcher -->
<div id="launcher" class="launcher hidden">
    <div class="launcher-message">Cadastro editado com sucesso!</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script><script src="js/tabela_visitantes.js"></script>


</body>
</html>
