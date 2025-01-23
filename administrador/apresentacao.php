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
        <title>Tabela de apresentação</title>
        <link rel="icon" href="../images/favicon.ico" type="image/png">
        <link rel="stylesheet" href="css/apresentacao.css">
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
        <h2 class="section-title">[Administrador] Tabela de apresentação</h2> <!-- Título separado -->

        <!-- Estrutura do título e campo de busca na mesma linha -->
        <div class="search-container">

        <form class="search-form" method="get" action="">
    <label for="criterio">Buscar por</label>
    <select name="criterio" id="criterio">
        <option value="id" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'id') ? 'selected' : ''; ?>>ID</option>
        <option value="data" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'data') ? 'selected' : ''; ?>>DATA</option>
        <option value="porteiro" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'porteiro') ? 'selected' : ''; ?>>PORTEIRO</option>
        <option value="horario_apresentacao" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'horario_apresentacao') ? 'selected' : ''; ?>>HORÁRIO</option>
        <option value="nome" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'nome') ? 'selected' : ''; ?>>NOME</option>
        <option value="empresa" <?php echo (isset($_GET['criterio']) && $_GET['criterio'] == 'empresa') ? 'selected' : ''; ?>>EMPRESA</option>
    </select>

    <div class="input-container">
        <input type="text" name="busca" id="campo-busca" placeholder="Digite sua busca" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
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
                        <th>HORARIO DE APRESENTAÇÃO</th>
                        <th>NOME</th>
                        <th>EMPRESA</th>
                        <th>AÇÕES</th>
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
                        $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa
                            FROM apresentacao ";

                        // Aplicando o filtro, se houver
                        if (!empty($criterio) && !empty($busca)) {
                        $sql .= " WHERE $criterio LIKE ? ";  // Corrigido o espaço antes de WHERE
                        }

                        // Ordenando os resultados
                        $sql .= " ORDER BY id DESC";  // Corrigido o espaço antes de ORDER

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
                            $horario_apresentacao = formatarHorario($row['horario_apresentacao']);

                            // Formata a data para o formato dd-mm-aaaa
                            $data = !empty($row['data']) ? DateTime::createFromFormat('Y-m-d', $row['data'])->format('d/m/Y') : '-';

                            echo '<tr data-id="' . $row['id'] . '">';
                            // Exibe apenas o valor no tooltip, não o nome do campo
                            echo '<td title="' . (empty($row['id']) ? '-' : $row['id']) . '">' . $row['id'] . '</td>';
                            echo '<td title="' . (empty($row['data']) ? '-' : $data) . '">' . (empty($row['data']) ? '-' : $data) . '</td>';
                            echo '<td title="' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '">' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '</td>';
                            echo '<td title="' . (empty($row['horario_apresentacao']) ? '-' : $horario_apresentacao) . '">' . (empty($row['horario_apresentacao']) ? '-' : $horario_apresentacao) . '</td>';
                            echo '<td title="' . (empty($row['nome']) ? '-' : $row['nome']) . '">' . (empty($row['nome']) ? '-' : $row['nome']) . '</td>';
                            echo '<td title="' . (empty($row['empresa']) ? '0' : $row['empresa']) . '">' . (empty($row['empresa']) ? '-' : $row['empresa']) . '</td>';
                            echo '<td class="actions">
                                    <button class="edit-button" data-id="' . $row['id'] . '" 
                                        data-porteiro="' . htmlspecialchars($row['porteiro'], ENT_QUOTES) . '"
                                        data-nome="' . htmlspecialchars($row['nome'], ENT_QUOTES) . '"
                                        data-empresa="' . htmlspecialchars($row['empresa'], ENT_QUOTES) . '"
                                        data-horario_apresentacao="' . $horario_apresentacao . '">Editar</button>
                                    <button class="delete-button" data-id="' . $row['id'] . '">Excluir</button>
                                </td>';
                            echo '</tr>';
                        }
                        } else {
                        echo '<tr><td colspan="6">Nenhum registro de apresentação encontrado.</td></tr>';  // Corrigido o colspan para 6
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
                    <option value="999">999</option>
                    <option value="9999">9999</option>
                    <option value="999999">999999</option>
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
            <label for="horario_apresentacao">Hora da apresentação</label>
            <input type="text" id="horario_apresentacao">
            
            <label for="nome">Nome</label>
            <input type="text" id="nome">
                    
            <label for="empresa">Destino:</label>
            <input type="text" id="empresa">
                        
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
        <script src="js/apresentacao.js"></script>
    </body>
</html>