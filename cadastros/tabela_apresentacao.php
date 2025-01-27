<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de apresentação</title>
    <link rel="icon" href="../images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="css/tabela_apresentacao.css">
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
                <h2>Tabela de apresentação</h2>
            </div>

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
                        </tr>
                    </thead>
                    <tbody id="veiculos-list">

                    <?php
                        require '../db_config.php';

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                        }
                        
                        $criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'id';
                        $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
                        
                        // Verifica se o critério é 'data' e converte o formato se necessário
                        if ($busca !== '') {
                            if ($criterio === 'data') {
                                // Converte a data de dd/mm/yyyy para yyyy-mm-dd
                                $dataConvertida = date('Y-m-d', strtotime(str_replace('/', '-', $busca)));
                                $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa FROM apresentacao WHERE data = '$dataConvertida' ORDER BY id DESC";
                            } elseif ($criterio === 'id') {
                                $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa FROM apresentacao WHERE id = $busca ORDER BY id DESC";
                            } elseif ($criterio === 'km_saida' || $criterio === 'km_chegada') {
                                $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa FROM apresentacao WHERE $criterio = '$busca' ORDER BY id DESC";
                            } else {
                                $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa FROM apresentacao WHERE $criterio LIKE '%$busca%' ORDER BY id DESC";
                            }
                        } else {
                            $sql = "SELECT id, data, porteiro, horario_apresentacao, nome, empresa FROM apresentacao ORDER BY id DESC";
                        }
                        
                        // Resto do seu código para executar a consulta
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr data-id="' . $row['id'] . '">';
                                echo '<td title="' . htmlspecialchars($row['id']) . '">' . $row['id'] . '</td>'; 
                                echo '<td title="' . date('d/m/Y', strtotime($row['data'])) . '">' . date('d/m/Y', strtotime($row['data'])) . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['porteiro']) . '">' . $row['porteiro'] . '</td>'; 
                                echo '<td title="' . date('H:i', strtotime($row['horario_apresentacao'])) . '">' . date('H:i', strtotime($row['horario_apresentacao'])) . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['nome']) . '">' . $row['nome'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['empresa']) . '">' . $row['empresa'] . '</td>'; 
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="12">Nenhum registro cadastrado.</td></tr>';
                        }
                        
                        $conn->close();
                        
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

    <div id="launcher" class="launcher hidden">
        <div class="launcher-message">Cadastro editado com sucesso!</div>
    </div>

    <script src="js/tabela_apresentacao.js"></script>
    
</body>
</html>
