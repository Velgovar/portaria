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
$criteriosValidos = ['id', 'data', 'porteiro', 'veiculo', 'km_saida', 'km_chegada', 'horario_saida', 'horario_chegada', 'destino', 'motivo', 'acao'];
if (!in_array($criterio, $criteriosValidos)) {
    $criterio = 'id';
}

// Consultar o total de registros com base no critério de busca
$stmt = $pdo->prepare("SELECT COUNT(*) FROM registros_veiculos WHERE $criterio LIKE :busca");
$stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
$stmt->execute();
$totalRegistros = $stmt->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consultar os registros com base na página atual e critério de busca
$stmt = $pdo->prepare("SELECT * FROM registros_veiculos WHERE $criterio LIKE :busca ORDER BY id DESC LIMIT :offset, :limit");
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
    <link rel="icon" href="../images/favicon.ico" type="image/png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="css/tabela_veiculos.css">

</head>
<body>
<img src="../images/inusitta.png" class="image-center" alt="icone central">

    <a href="../menu.html" class="style">Voltar</a>

    <div class="container_table1">
        <div class="header">
            <h2>Tabela Veículos</h2>
        </div>

        <form class="search-form" method="get" action="">
    <label for="campo-busca">Buscar por</label>
    <select name="criterio" id="criterio">
        <option value="id">ID</option>
        <option value="data">DATA</option>
        <option value="porteiro">PORTEIRO</option>
        <option value="veiculo">VEÍCULO</option>
        <option value="km_saida">KM SAIDA</option>
        <option value="km_chegada">KM CHEGADA</option>
        <option value="horario_saida">HORARIO SAIDA</option>
        <option value="horario_chegada">HORARIO CHEGADA</option>
        <option value="destino">DESTINO</option>
        <option value="motivo">MOTIVO</option>
        <option value="acao">AÇÃO</option>
    </select>
    <div class="input-container">
        <input type="text" name="busca" id="campo-busca" placeholder="Digite sua busca">
        <button type="submit">
            <i class="fas fa-search search-icon"></i>
        </button>
    </div>
</form>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DATA</th>
                        <th>PORTEIRO</th>
                        <th>VEÍCULO</th>
                        <th>KM SAIDA</th>
                        <th>KM CHEGADA</th>
                        <th>HORARIO SAIDA</th>
                        <th>HORARIO CHEGADA</th>
                        <th>DESTINO</th>
                        <th>MOTIVO</th>
                        <th>AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr data-id="<?php echo htmlspecialchars($registro['id']); ?>">
                            <td><?php echo htmlspecialchars($registro['id']); ?></td>
                            <td><?php echo htmlspecialchars($registro['data']); ?></td>
                            <td><?php echo htmlspecialchars($registro['porteiro']); ?></td>
                            <td><?php echo htmlspecialchars($registro['veiculo']); ?></td>
                            <td><?php echo htmlspecialchars($registro['km_saida']); ?></td>
                            <td class="km_chegada"><?php echo htmlspecialchars(!empty($registro['km_chegada']) ? $registro['km_chegada'] : '0'); ?></td>
                            <td><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_saida']))); ?></td>
                            <td class="horario_chegada"><?php echo htmlspecialchars(date('H:i', strtotime($registro['horario_chegada']))); ?></td>
                            <td><?php echo htmlspecialchars($registro['destino']); ?></td>
                            <td><?php echo htmlspecialchars($registro['motivo']); ?></td>
                            <td>
                                <button class="edit-button" onclick="openModal(
                                    <?php echo htmlspecialchars($registro['id']); ?>, 
                                    '<?php echo htmlspecialchars($registro['km_chegada']); ?>', 
                                    '<?php echo htmlspecialchars($registro['horario_chegada']); ?>'
                                )">Editar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                    <span>Linhas / Paginas</span>
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

            <div class="cancel-message" id="cancelMessage"></div>

            <?php if ($mensagemSucesso): ?>
                <div class="success-message"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
            <?php endif; ?>
        </div>

        <div class="modal" id="editModal">
            <div class="modal-content">
                <h2>Editar Registro</h2>
                <form id="editForm" action="../config/editar_table_veiculos.php" method="POST">
                <input type="hidden" id="editId" name="id">
            
            <label for="editKmChegada">KM Chegada </label>
        <div class="input-icon">
            <input type="text" id="editKmChegada" name="km_chegada"  required>
            <i class="fas fa-tachometer-alt"></i> 
        </div>
                        
            <label for="editHorarioChegada">Horário Chegada</label>
        <div class="input-icon">
            <i class="fas fa-clock"></i> 
            <input type="number" id="editHorarioChegada" name="horario_chegada" required>
        </div>

            <input type="text" class="hidden" id="editData" name="data">
            <input type="text" class="hidden" id="editPorteiro" name="porteiro">
            <input type="text" class="hidden" id="editVeiculo" name="veiculo">
            <input type="text" class="hidden" id="editKmSaida" name="km_saida">
            <input type="text" class="hidden" id="editDestino" name="destino">
            <input type="text" class="hidden" id="editMotivo" name="motivo">
            
            <button type="submit" class="salvar">Salvar</button>
            <button type="button" class="cancelar" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<div id="launcher" class="launcher hidden">
    <div class="launcher-message">Cadastro editado com sucesso!</div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="js/tabela_Veiculos.js"></script>

</body>
</html>
