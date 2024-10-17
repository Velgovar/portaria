<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veículos de Terceiros</title>
    <link rel="icon" href="../images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="css/tabela_veiculos.css">
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
                <h2>Cadastros de veículos de terceiros</h2>
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

                        $criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'id';
                        $busca = isset($_GET['busca']) ? $_GET['busca'] : '';

                        if ($busca !== '') {
                            if ($criterio === 'id') {
                                $sql = "SELECT id, data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo FROM registros_veiculos WHERE id = $busca ORDER BY id DESC";
                            } elseif ($criterio === 'km_saida' || $criterio === 'km_chegada') {
                                $sql = "SELECT id, data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo FROM registros_veiculos WHERE $criterio = '$busca' ORDER BY id DESC";
                            } else {
                                $sql = "SELECT id, data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo FROM registros_veiculos WHERE $criterio LIKE '%$busca%' ORDER BY id DESC";
                            }
                        } else {
                            $sql = "SELECT id, data, porteiro, veiculo, motorista, km_saida, km_chegada, horario_saida, horario_chegada, destino, motivo FROM registros_veiculos ORDER BY id DESC";
                        }

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr data-id="' . $row['id'] . '">';
                                echo '<td title="' . htmlspecialchars($row['id']) . '">' . $row['id'] . '</td>'; 
                                echo '<td title="' . date('d-m-Y', strtotime($row['data'])) . '">' . date('d-m-Y', strtotime($row['data'])) . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['porteiro']) . '">' . $row['porteiro'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['veiculo']) . '">' . $row['veiculo'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['motorista']) . '">' . $row['motorista'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['km_saida']) . '">' . $row['km_saida'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['km_chegada']) . '">' . $row['km_chegada'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars(date('H:i', strtotime($row['horario_saida']))) . '">' . date('H:i', strtotime($row['horario_saida'])) . '</td>'; 
                                echo '<td title="' . htmlspecialchars(date('H:i', strtotime($row['horario_chegada']))) . '">' . date('H:i', strtotime($row['horario_chegada'])) . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['destino']) . '">' . $row['destino'] . '</td>'; 
                                echo '<td title="' . htmlspecialchars($row['motivo']) . '">' . $row['motivo'] . '</td>'; 
                                echo '<td><button class="edit-button" data-id="' . $row['id'] . '" data-km-chegada="' . $row['km_chegada'] . '" data-horario-chegada="' . $row['horario_chegada'] . '">Editar</button></td>'; 
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

    <div id="modal-edit" class="modal">
    <div class="modal-content">
        <h2>Editar Registro</h2>
        <form id="editForm">
            <input type="hidden" id="editId" name="id">
            <label for="editKmChegada">KM Chegada:</label>
            <input type="text" id="editKmChegada" name="km_chegada" placeholder="Digite KM" pattern="\d*" title="Apenas números são permitidos" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <label for="editHorarioChegada">Horário Chegada:</label>
            <input type="text" id="editHorarioChegada" name="horario_chegada" placeholder="HH:MM">
            <button type="submit" class="salvar">Salvar</button>
            <button type="button" class="cancelar" onclick="closeEditModal()">Cancelar</button>
        </form>
    </div>
</div>

    <div id="launcher" class="launcher hidden">
        <div class="launcher-message">Cadastro editado com sucesso!</div>
    </div>

    <script src="js/tabela_Veiculos.js"></script>
    
</body>
</html>
