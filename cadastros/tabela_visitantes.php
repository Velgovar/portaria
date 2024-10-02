<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada e saída de terceiros</title>
    <link rel="icon" href="../images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="css/tabela_visitantes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    
    <div class="setor-container">
        <div class="fixed-container">
            <form id="vigia-form" method="POST" action="config/setores_config.php">
                <div class="form-group">
                    <div id="nome-error" class="error-message"></div>
                </div>
                <div class="button-container"></div>
            </form>
        </div>

        <a href="../menu.html" class="voltar">Voltar</a>

        <div class="table-container">
            <div class="table-header">
                <h2>Cadastros de entrada e saída de terceiros</h2>
            </div>

            <form class="search-form" method="get" action="">
    <label for="campo-busca">Buscar por</label>
    <select name="criterio" id="criterio">
        <option value="id">ID</option>
        <option value="data">DATA</option>
        <option value="porteiro">PORTEIRO</option>
        <option value="veiculo">VEÍCULO</option>
        <option value="motorista">MOTORISTA</option>
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
            <div class="scrollable-container">
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
                    <tbody id="setor-list">
                    <?php
require '../db_config.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Query para selecionar registros com base na busca
$criterio = isset($_GET['criterio']) ? $conn->real_escape_string($_GET['criterio']) : 'id';
$busca = isset($_GET['busca']) ? $conn->real_escape_string($_GET['busca']) : '';

// Ajuste na consulta SQL para tratamento do ID e valores numéricos
if ($busca !== '') {
    if ($criterio === 'id') {
        // Verifica se a busca é um número e prepara a consulta
        $sql = "SELECT id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor 
                FROM registro 
                WHERE id = $busca 
                ORDER BY id DESC";
    } elseif ($criterio === 'cpf' || $criterio === 'placa') {
        // Para CPF e placa, usa a comparação exata
        $sql = "SELECT id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor 
                FROM registro 
                WHERE $criterio = '$busca' 
                ORDER BY id DESC";
    } else {
        // Para outros critérios, usa LIKE
        $sql = "SELECT id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor 
                FROM registro 
                WHERE $criterio LIKE '%$busca%' 
                ORDER BY id DESC";
    }
} else {
    $sql = "SELECT id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor 
            FROM registro 
            ORDER BY id DESC";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
// Exibe os registros em uma tabela
// Exibe os registros em uma tabela
while ($row = $result->fetch_assoc()) {
    echo '<tr data-id="' . $row['id'] . '">';
    echo '<td title="' . $row['id'] . '">' . $row['id'] . '</td>';
    echo '<td title="' . date('d-m-Y', strtotime($row['data'])) . '">' . date('d-m-Y', strtotime($row['data'])) . '</td>';
    echo '<td title="' . $row['porteiro'] . '">' . $row['porteiro'] . '</td>';
    echo '<td title="' . $row['nome'] . '">' . $row['nome'] . '</td>';
    echo '<td title="' . $row['cpf'] . '">' . $row['cpf'] . '</td>';
    echo '<td title="' . $row['tipovisitante'] . '">' . $row['tipovisitante'] . '</td>';
    echo '<td title="' . $row['servico'] . '">' . $row['servico'] . '</td>';
    echo '<td title="' . $row['empresa'] . '">' . $row['empresa'] . '</td>';
    echo '<td title="' . $row['estacionamento'] . '">' . $row['estacionamento'] . '</td>';
    echo '<td title="' . (!empty($row['placa']) ? $row['placa'] : '-') . '">' . (!empty($row['placa']) ? $row['placa'] : '-') . '</td>'; // Modificação feita aqui
    echo '<td title="' . date('H:i', strtotime($row['horario_entrada'])) . '">' . date('H:i', strtotime($row['horario_entrada'])) . '</td>';
    echo '<td title="' . date('H:i', strtotime($row['horario_saida'])) . '">' . date('H:i', strtotime($row['horario_saida'])) . '</td>';
    echo '<td title="' . $row['colaborador'] . '">' . $row['colaborador'] . '</td>';
    echo '<td title="' . $row['setor'] . '">' . $row['setor'] . '</td>';
    echo '<td><button class="edit-button" data-id="' . $row['id'] . '">Editar</button></td>';
    echo '</tr>';
}
} else {
    echo '<tr><td colspan="15">Nenhum registro cadastrado.</td></tr>';
}

$conn->close();


?>
                    </tbody>
                </table>
            </div>

            <!-- Select para escolher o número de linhas a serem exibidas -->
            <div class="select-container">
                <select id="rows-per-page" onchange="updateRows()">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="100">100</option>
                    <option value="1000">1000</option>
                </select>
                <span> linhas / páginas</span>
            </div>

            <div class="pagination-container">
                <div class="pagination">
                    <a href="#" id="first-page" class="disabled">&laquo;</a>
                    <a href="#" id="prev-page" class="disabled">&lt;</a>
                    <div id="page-links"></div>
                    <a href="#" id="next-page">&gt;</a>
                    <a href="#" id="last-page">&raquo;</a>
                </div>
            </div>
        </div>
    </div>

<!-- Modal de Edição -->
<div id="modal-edit" class="modal">
    <div class="modal-content">
        <h2>Editar Registro</h2>
        <form id="editForm">
            <input type="hidden" id="editId" name="id">
            <label for="editHorarioSaida">Horário Saída:</label>
            <input type="text" id="editHorarioSaida" name="horario_saida" placeholder="HH:MM" oninput="formatarHorario(this)">
            <button type="submit" class="salvar">Salvar</button>
            <button type="button" class="cancelar" onclick="closeEditModal()">Cancelar</button>
        </form>
    </div>
</div>

    <div id="launcher" class="launcher hidden">
        <div class="launcher-message">Cadastro editado com sucesso!</div>
    </div>

    <script src="js/tabela_visitantes.js"></script>
</body>
</html>
