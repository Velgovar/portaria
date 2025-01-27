<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Veículos de Terceiros</title>
        <link rel="icon" href="../images/favicon.ico" type="image/png">
        <link rel="stylesheet" href="css/tabela_veiculos.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        </head>
        <body>
    <div class="setor-container">
        <div class="fixed-container">
            <form id="vigia-form" method="POST" action="config/setores_config.php">
                <div class="form-group">
                </div>
            </form>
        </div>

        <a href="menu.php" class="voltar">Voltar</a>

        <!-- Título principal da seção -->
        <h2 class="section-title">[Administrador] Cadastros de veículos de terceiros</h2> <!-- Título separado -->

        <!-- Estrutura do título e campo de busca na mesma linha -->
        <div class="search-container">

        <form class="search-form" method="get" action="">
    <label for="search-select">Buscar por</label>
    <select name="criterio" id="search-select">
        <option value="id" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'id') ? 'selected' : ''; ?>>ID</option>
        <option value="data" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'data') ? 'selected' : ''; ?>>DATA</option>
        <option value="porteiro" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'porteiro') ? 'selected' : ''; ?>>PORTEIRO</option>
        <option value="veiculo" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'veiculo') ? 'selected' : ''; ?>>VEÍCULO</option>
        <option value="motorista" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'motorista') ? 'selected' : ''; ?>>MOTORISTA</option>
        <option value="km_saida" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'km_saida') ? 'selected' : ''; ?>>KM SAÍDA</option>
        <option value="km_chegada" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'km_chegada') ? 'selected' : ''; ?>>KM CHEGADA</option>
        <option value="horario_saida" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'horario_saida') ? 'selected' : ''; ?>>HORÁRIO SAÍDA</option>
        <option value="horario_chegada" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'horario_chegada') ? 'selected' : ''; ?>>HORÁRIO CHEGADA</option>
        <option value="destino" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'destino') ? 'selected' : ''; ?>>DESTINO</option>
        <option value="motivo" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'motivo') ? 'selected' : ''; ?>>MOTIVO</option>
    </select>

    <div class="input-container">
        <input type="text" name="busca" id="search-input" placeholder="Digite sua busca" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
        <button type="submit">
            <i class="fas fa-search search-icon"></i>
        </button>
    </div>
</form>


            <div class="table-container">
                <div class="table-header">
                    <thead>
                </div>
                <div class="scrollable-container">
            <table>
                
                <thead>
                        <tr>
                            <th>ID</th>
                            <th>DATA</th>
                            <th>PORTEIRO</th>
                            <th>VEÍCULO</th>
                            <th>MOTORISTA</th>
                            <th>KM SAÍDA</th>
                            <th>KM CHEGADA</th>
                            <th>HORÁRIO SAÍDA</th>
                            <th>HORÁRIO CHEGADA</th>
                            <th>DESTINO</th>
                            <th>MOTIVO</th>
                            <th>AÇÃO</th>
                        </tr>
                </thead>
                
                <tbody id="veiculos-list">
                <?php
require '../db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Definindo o critério de busca e o termo de busca
$criterio = isset($_GET['criterio']) ? $_GET['criterio'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Verifica se o critério de busca é 'data'
if (!empty($criterio) && !empty($busca) && $criterio == 'data') {
    // Converte a data de d/m/Y para Y-m-d
    $data = DateTime::createFromFormat('d/m/Y', $busca);
    if ($data) {
        $busca = $data->format('Y-m-d');  // Converte para o formato que o banco de dados espera
    } else {
        $busca = '';  // Caso a data esteja no formato errado
    }
}

// Base da consulta SQL
$sql = "SELECT id, data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo 
        FROM registros_veiculos ";

// Aplicando o filtro, se houver
if (!empty($criterio) && !empty($busca)) {
    $sql .= "WHERE $criterio LIKE ? ";  // Usando 'LIKE' para permitir busca parcial
}

// Ordenando os resultados
$sql .= "ORDER BY id DESC";

// Preparando a consulta
$stmt = $conn->prepare($sql);

// Se houver um filtro, vinculamos o valor da busca
if (!empty($criterio) && !empty($busca)) {
    $busca = "%" . $busca . "%";  // Adiciona os percentuais (%) para o LIKE
    $stmt->bind_param("s", $busca);  // Binding do valor da busca
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Formata os horários removendo os segundos
        $horario_saida = formatarHorario($row['horario_saida']);
        $horario_chegada = formatarHorario($row['horario_chegada']);

        // Formata a data para o formato dd-mm-aaaa
        $data = !empty($row['data']) ? DateTime::createFromFormat('Y-m-d', $row['data'])->format('d/m/Y') : '-';

        echo '<tr data-id="' . $row['id'] . '">';
        // Exibe apenas o valor no tooltip, não o nome do campo
        echo '<td title="' . (empty($row['id']) ? '-' : $row['id']) . '">' . $row['id'] . '</td>';
        echo '<td title="' . (empty($row['data']) ? '-' : $data) . '">' . (empty($row['data']) ? '-' : $data) . '</td>';
        echo '<td title="' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '">' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '</td>';
        echo '<td title="' . (empty($row['veiculo']) ? '-' : $row['veiculo']) . '">' . (empty($row['veiculo']) ? '-' : $row['veiculo']) . '</td>';
        echo '<td title="' . (empty($row['motorista']) ? '-' : $row['motorista']) . '">' . (empty($row['motorista']) ? '-' : $row['motorista']) . '</td>';
        echo '<td title="' . (empty($row['km_saida']) ? '0' : $row['km_saida']) . '">' . (empty($row['km_saida']) ? '0' : $row['km_saida']) . '</td>';
        echo '<td title="' . (empty($row['km_chegada']) ? '0' : $row['km_chegada']) . '">' . (empty($row['km_chegada']) ? '0' : $row['km_chegada']) . '</td>';
        echo '<td title="' . (empty($horario_saida) ? '00:00' : $horario_saida) . '">' . (empty($horario_saida) ? '00:00' : $horario_saida) . '</td>';
        echo '<td title="' . (empty($horario_chegada) ? '00:00' : $horario_chegada) . '">' . (empty($horario_chegada) ? '00:00' : $horario_chegada) . '</td>';
        echo '<td title="' . (empty($row['destino']) ? '-' : $row['destino']) . '">' . (empty($row['destino']) ? '-' : $row['destino']) . '</td>';
        echo '<td title="' . (empty($row['motivo']) ? '-' : $row['motivo']) . '">' . (empty($row['motivo']) ? '-' : $row['motivo']) . '</td>';
        echo '<td class="actions">
                <button class="edit-button" data-id="' . $row['id'] . '" 
                    data-porteiro="' . htmlspecialchars($row['porteiro'], ENT_QUOTES) . '"
                    data-veiculo="' . htmlspecialchars($row['veiculo'], ENT_QUOTES) . '"
                    data-motorista="' . htmlspecialchars($row['motorista'], ENT_QUOTES) . '"
                    data-horario_saida="' . $horario_saida . '"
                    data-horario_chegada="' . $horario_chegada . '">Editar</button>
                <button class="delete-button" data-id="' . $row['id'] . '">Excluir</button>
            </td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="12">Nenhum registro de veículo encontrado.</td></tr>';
}

$conn->close();


// Função para formatar o horário, removendo os segundos
function formatarHorario($horario) {
    // Verifica se o horário contém a separação de minutos
    if (strpos($horario, ':') !== false) {
        // Divide o horário e pega apenas a parte de horas e minutos
        $partes = explode(':', $horario);
        return $partes[0] . ':' . $partes[1]; // Retorna apenas horas e minutos
    }
    return $horario; // Se não contiver, retorna o valor original
}
?>

</tbody>




            </table>
        </div>
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

 
<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h2>Editar Registro</h2>
        <form id="editForm">
            <div class="data-porteiro">
                <div class="data-field">
                    <label for="data">Data:</label>
                    <input type="text" id="data" placeholder="dd/mm/aaaa">
                    </div>
                <div class="porteiro-field">
                    <label for="porteiro">Porteiro:</label>
                    <input type="text" id="porteiro">
                </div>
            </div>           
            <label for="veiculo">Veículo:</label>
            <input type="text" id="veiculo">
            
            <label for="motorista">Motorista:</label>
            <input type="text" id="motorista">
            
            <div class="km">
                <div class="km-field">
                    <label for="km_saida">KM Saída:</label>
                    <input type="text" id="km_saida">
                </div>
                <div class="km-field">
                    <label for="km_chegada">KM Chegada:</label>
                    <input type="text" id="km_chegada">
                </div>
            </div>            
            <div class="horarios">
                <div class="horario">
                    <label for="horario_saida">Horário Saída:</label>
                    <input type="text" id="horario_saida">
                </div>
                <div class="horario">
                    <label for="horario_chegada">Horário Chegada:</label>
                    <input type="text" id="horario_chegada">
                </div>
            </div>            
            <label for="destino">Destino:</label>
            <input type="text" id="destino">
            
            <label for="motivo">Motivo:</label>
            <input type="text" id="motivo">
            
            <div class="modal-buttons">
                <button type="submit" id="save-btn" class="salvar">Salvar</button>
                <button type="button" id="cancelButton" class="cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <h3>Confirmação de Exclusão</h3>
        <p>Você tem certeza que deseja excluir este cadastro?</p>
        <form id="confirmationForm">
            <input type="text" id="confirmationInput" autocomplete="off" placeholder="Digite 'excluir' para confirmar">
            
            <!-- Botão de Exclusão com a classe delete-button -->
            <button type="button" class="delete-button" data-id="1">Excluir</button>

            <!-- Botão de Cancelar com a classe cancel -->
            <button type="button" class="cancel" onclick="closeConfirmationModal()">Cancelar</button>
        </form>
    </div>
</div>

<!-- Launcher para notificação -->
<div id="launcher" class="launcher hidden">
    <div class="launcher-message">Cadastro editado com sucesso!</div>
</div>



        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/tabela_veiculos.js"></script>
    </body>
</html>