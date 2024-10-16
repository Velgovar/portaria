<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada e saída de terceiros</title>
    
    <link rel="icon" href="../images/favicon.ico" type="image/png">

    <link rel="stylesheet" href="css/visitantes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<body>

    <a href="../menu.html" class="voltar">Voltar</a>

    <div class="container">
                    <img src="../images/inusitta.png" class="image-center" alt="icone central">
                    <h2>Controle de entrada e saída de terceiros</h2>
                    <form id="vehicle-form" method="POST" action="../config/config.php" autocomplete="off">
                        <div class="form-group data-porteiro">
            <div class="data-item"> 
                <label for="date"> Data</label>
                <div class="input-container">
                    <input type="text" id="date" name="data" class="datepicker" placeholder="Selecione a data">
                    <i class="fas fa-calendar-alt calendar-icon"></i>
                </div>
            </div>

                <div class="porteiro-item">
                    <label for="porteiro">Porteiro/Vigia:</label>
                    <select id="porteiro" name="porteiro">
                    <option value="">Selecione um porteiro/vigia</option>
                            <?php
                            // Exibir erros de PHP
                            ini_set('display_errors', 1);
                            ini_set('display_startup_errors', 1);
                            error_reporting(E_ALL);

                            require '../db_config.php';

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                            }

                            // Modifique a consulta SQL para incluir ORDER BY
                            $sql = "SELECT id, nome FROM porteiros ORDER BY nome ASC"; // Ordem alfabética
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['nome']) . '">' . htmlspecialchars($row['nome']) . '</option>';
                                }
                            } else {
                                echo '<option value="">Nenhum porteiro/vigia encontrado</option>';
                            }

                            $conn->close();
                            ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome completo">
            </div>

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="Ex: 123.456.789-00">
            </div>

            <div class="form-group">
                <label for="tipovisitante">Tipo de visitante</label>
                <select type="text" id="tipovisitante" name="tipovisitante">
                    <option value="">Selecione o tipo de visitante</option>
                    <?php
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    require '../db_config.php'; 

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                    }

                    // Modifique a consulta SQL para incluir ORDER BY
                    $sql = "SELECT id, nome FROM tipovisitante ORDER BY nome ASC"; // Ordem alfabética
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['nome']) . '">' . htmlspecialchars($row['nome']) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhum Tipo de visitante encontrado</option>';
                    }

                    $conn->close();
                    ?>
                </select>
            </div>


            <div class="form-group">
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa" placeholder="Ex: Inusittá">
            </div>

            <div class="form-group">
                <label for="servico">Motivo da visita</label>
                <input type="text" id="servico" name="servico" placeholder="Ex: Visitante">
            </div>

            <div class="form-group km">
                <div class="km-item">
                    <label for="estacionamento">Usou estacionamento</label>
                    <select type="text" id="estacionamento" name="estacionamento">
                    <option value="">Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                    </select>
                </div>
                <div class="km-item" style="display: none;">
                    <label for="placa">Placa</label>
                    <input type="text" id="placa" name="placa" maxlength="8" placeholder="Mercosul (ABC1D23) ou padrão antiga (ABC-1234)">
                </div>
            </div>

            
            <div class="form-group horario">
    <div class="horario-item">
        <label for="horario_entrada"> Horário entrada</label>
        <div class="input-icon">
            <i class="fas fa-clock"></i> <!-- Ícone de Relógio -->
            <input type="text" id="horario_entrada" name="horario_entrada" placeholder="Ex: 12:00">
        </div>
    </div>
    <div class="horario-item">
        <label for="horario_saida"> Horário saída</label>
        <div class="input-icon">
            <i class="fas fa-clock"></i> <!-- Ícone de Relógio -->
            <input type="text" id="horario_saida" name="horario_saida" placeholder="Ex: 12:00">
        </div>
    </div>
</div>


            <div class="form-group km">
                <div class="km-item">
                    <label for="colaborador">Colaborador responsável pela liberação</label>
                    <input type="text" id="colaborador" name="colaborador" placeholder="Colaborador responsável">
                </div>
                <div class="km-item">
                <label for="setor">Setor de destino</label>
                <select type="text" id="setor" name="setor">
                    <option value="">Selecione um setor</option>
                    <?php
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        error_reporting(E_ALL);

                        require '../db_config.php'; 

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                        }

                        // Modifique a consulta SQL para incluir ORDER BY
                        $sql = "SELECT id, nome FROM setores ORDER BY nome ASC"; // Ordem alfabética
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['nome']) . '">' . htmlspecialchars($row['nome']) . '</option>';
                            }
                        } else {
                            echo '<option value="">Nenhum Setor encontrado</option>';
                        }

                        $conn->close();
                    ?>
                </select>
            </div>

            </div>

            <div class="button-container">
                <button type="submit" id="submit-btn" class="styled">Salvar</button>
            </div>

        </form>
    </div>

        <div id="launcher" class="launcher hidden">
            <div class="launcher-message">Cadastro editado com sucesso!</div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>    
    <script src="js/visitantes.js"></script>
    
</body>
</html>
