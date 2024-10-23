<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Entrada e saída de terceiros</title>
        <link rel="icon" href="../images/favicon.ico" type="image/png">
        <link rel="stylesheet" href="css/tabela_visitantes.css">
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
        <h2 class="section-title">[Administrador] Cadastros de entrada e saída de terceiros</h2> <!-- Título separado -->

        <!-- Estrutura do título e campo de busca na mesma linha -->
        <div class="search-container">

            <form class="search-form" method="get" action="">
                <label for="search-select">Buscar por</label>
                <select name="criterio" id="search-select">
                    <option value="id" <?php echo ($criterio == 'id') ? 'selected' : ''; ?>>ID</option>
                    <option value="data" <?php echo ($criterio == 'data') ? 'selected' : ''; ?>>DATA</option>
                    <option value="porteiro" <?php echo ($criterio == 'porteiro') ? 'selected' : ''; ?>>PORTEIRO</option>
                    <option value="nome" <?php echo ($criterio == 'nome') ? 'selected' : ''; ?>>NOME</option>
                    <option value="cpf" <?php echo ($criterio == 'cpf') ? 'selected' : ''; ?>>CPF</option>
                    <option value="tipovisitante" <?php echo ($criterio == 'tipovisitante') ? 'selected' : ''; ?>>TIPO</option>
                    <option value="km_chegada" <?php echo ($criterio == 'km_chegada') ? 'selected' : ''; ?>>KM CHEGADA</option>
                    <option value="horario_saida" <?php echo ($criterio == 'horario_saida') ? 'selected' : ''; ?>>HORÁRIO SAÍDA</option>
                    <option value="horario_chegada" <?php echo ($criterio == 'horario_chegada') ? 'selected' : ''; ?>>HORÁRIO CHEGADA</option>
                    <option value="destino" <?php echo ($criterio == 'destino') ? 'selected' : ''; ?>>DESTINO</option>
                    <option value="motivo" <?php echo ($criterio == 'motivo') ? 'selected' : ''; ?>>MOTIVO</option>
                </select>

                <div class="input-container">
                    <input type="text" name="busca" id="search-input" placeholder="Digite sua busca">
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
                    <th>NOME</th> <!-- Novo campo -->
                    <th>CPF</th> <!-- Novo campo -->
                    <th>TIPO VISITANTE</th> <!-- Novo campo -->
                    <th>SERVIÇO</th> <!-- Novo campo -->
                    <th>EMPRESA</th> <!-- Novo campo -->
                    <th>ESTACIONAMENTO</th> <!-- Novo campo -->
                    <th>PLACA</th> <!-- Novo campo -->
                    <th>HORÁRIO ENTRADA</th> <!-- Novo campo -->
                    <th>HORÁRIO SAÍDA</th>
                    <th>COLABORADOR</th> <!-- Novo campo -->
                    <th>SETOR</th> <!-- Novo campo -->
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
                        $sql = "SELECT id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horario_entrada, horario_saida, colaborador, setor 
                                FROM registro ";

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
                                $horario_entrada = formatarHorario($row['horario_entrada']);
                                $horario_saida = formatarHorario($row['horario_saida']);

                                // Formata a data para o formato dd-mm-aaaa
                                $data = !empty($row['data']) ? DateTime::createFromFormat('Y-m-d', $row['data'])->format('d/m/Y') : '-';

                                echo '<tr data-id="' . $row['id'] . '">';
                                echo '<td title="' . (empty($row['id']) ? '-' : $row['id']) . '">' . $row['id'] . '</td>';
                                echo '<td title="' . (empty($row['data']) ? '-' : $data) . '">' . (empty($row['data']) ? '-' : $data) . '</td>';
                                echo '<td title="' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '">' . (empty($row['porteiro']) ? '-' : $row['porteiro']) . '</td>';
                                echo '<td title="' . (empty($row['nome']) ? '-' : $row['nome']) . '">' . (empty($row['nome']) ? '-' : $row['nome']) . '</td>';
                                echo '<td title="' . (empty($row['cpf']) ? '-' : $row['cpf']) . '">' . (empty($row['cpf']) ? '-' : $row['cpf']) . '</td>';
                                echo '<td title="' . (empty($row['tipovisitante']) ? '-' : $row['tipovisitante']) . '">' . (empty($row['tipovisitante']) ? '-' : $row['tipovisitante']) . '</td>';
                                echo '<td title="' . (empty($row['servico']) ? '-' : $row['servico']) . '">' . (empty($row['servico']) ? '-' : $row['servico']) . '</td>';
                                echo '<td title="' . (empty($row['empresa']) ? '-' : $row['empresa']) . '">' . (empty($row['empresa']) ? '-' : $row['empresa']) . '</td>';
                                echo '<td title="' . (empty($row['estacionamento']) ? '-' : $row['estacionamento']) . '">' . (empty($row['estacionamento']) ? '-' : $row['estacionamento']) . '</td>';
                                echo '<td title="' . (empty($row['placa']) ? '-' : $row['placa']) . '">' . (empty($row['placa']) ? '-' : $row['placa']) . '</td>';
                                echo '<td title="' . (empty($horario_entrada) ? '00:00' : $horario_entrada) . '">' . (empty($horario_entrada) ? '00:00' : $horario_entrada) . '</td>';
                                echo '<td title="' . (empty($horario_saida) ? '00:00' : $horario_saida) . '">' . (empty($horario_saida) ? '00:00' : $horario_saida) . '</td>';
                                echo '<td title="' . (empty($row['colaborador']) ? '-' : $row['colaborador']) . '">' . (empty($row['colaborador']) ? '-' : $row['colaborador']) . '</td>';
                                echo '<td title="' . (empty($row['setor']) ? '-' : $row['setor']) . '">' . (empty($row['setor']) ? '-' : $row['setor']) . '</td>';
                                echo '<td class="actions">
                                        <button class="edit-button" data-id="' . $row['id'] . '" 
                                            data-porteiro="' . htmlspecialchars($row['porteiro'], ENT_QUOTES) . '"
                                            data-nome="' . htmlspecialchars($row['nome'], ENT_QUOTES) . '"
                                            data-cpf="' . htmlspecialchars($row['cpf'], ENT_QUOTES) . '"
                                            data-tipovisitante="' . htmlspecialchars($row['tipovisitante'], ENT_QUOTES) . '"
                                            data-servico="' . htmlspecialchars($row['servico'], ENT_QUOTES) . '"
                                            data-empresa="' . htmlspecialchars($row['empresa'], ENT_QUOTES) . '"
                                            data-estacionamento="' . htmlspecialchars($row['estacionamento'], ENT_QUOTES) . '"
                                            data-placa="' . htmlspecialchars($row['placa'], ENT_QUOTES) . '"
                                            data-horario_entrada="' . $horario_entrada . '"
                                            data-horario_saida="' . $horario_saida . '"
                                            data-colaborador="' . htmlspecialchars($row['colaborador'], ENT_QUOTES) . '"
                                            data-setor="' . htmlspecialchars($row['setor'], ENT_QUOTES) . '">Editar</button>
                                        <button class="delete-button" data-id="' . $row['id'] . '">Excluir</button>
                                    </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="15">Nenhum registro encontrado.</td></tr>';
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
                    <input type="text" id="data" placeholder="10/10/2024">
                </div>
                <div class="porteiro-field">
                    <label for="porteiro">Porteiro:</label>
                    <input type="text" id="porteiro" maxlength="255" placeholder="Ex: Eracildo Alves De Oliveira">
                </div>
            </div>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" maxlength="255" placeholder="Ex: Carlos Eduardo Santos">

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf"  maxlength="14" placeholder="Ex: 123.456.789-00">

            <label for="tipovisitante">Tipo de Visitante:</label>
            <input type="text" id="tipovisitante" maxlength="255" placeholder="Ex: Cliente">

            <label for="servico">Serviço:</label>
            <input type="text" id="servico" maxlength="255" placeholder="Ex: Visitante">


            <label for="empresa">Empresa:</label>
            <input type="text" id="empresa" maxlength="255" placeholder="Ex: Inusittá">

            <div class="horarios">
                <div class="horario">
                    <label for="estacionamento">Estacionamento:</label>
                    <input type="text" id="estacionamento" maxlength="3" placeholder="Colocar Sim ou Não">
                </div>
            <div class="horario">
                <label for="placa">Placa:</label>
                <input type="text" id="placa" maxlength="8" placeholder="(ABC1D23) ou (ABC-1234)">
            </div>
        </div>
            <div class="horarios">
                <div class="horario">
                    <label for="horario_entrada">Horário Entrada:</label>
                    <input type="text" id="horario_entrada" placeholder="Ex: 12:00">
                </div>
                <div class="horario">
                    <label for="horario_saida">Horário Saída:</label>
                    <input type="text" id="horario_saida" placeholder="Ex: 12:00">
                </div>
            </div>

            <label for="colaborador">Colaborador:</label>
            <input type="text" id="colaborador" maxlength="255" placeholder="Ana">

            <label for="setor">Setor:</label>
            <input type="text" id="setor" maxlength="255" placeholder="Ex: Almoxarifado">

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
        <script src="js/tabela_visitantes.js"></script>

    </body>
</html>