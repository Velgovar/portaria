<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Veículos</title>
    <link rel="stylesheet" href="../css/veiculos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    

</head>
<body>
                <a href="../menu.html" class="voltar">Voltar</a>

                <div class="container">
                    <img src="../images/inusitta.png" class="image-center" alt="icone central">
                    <h2>Controle de Veículos da Empresa</h2>
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
                    <label for="porteiro">Porteiro</label>
                    <select id="porteiro" name="porteiro">
                        <option value="">Selecione um porteiro</option>
                        <?php
                        // Exibir erros de PHP
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        error_reporting(E_ALL);

                        // Conexão com o banco de dados
                        $servername = "192.168.254.136";
                        $username = "felipe";
                        $password = "Aranhas12@";
                        $dbname = "cobra";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                        }

                        // Query para buscar os porteiro/vigias cadastrados
                        $sql = "SELECT id, nome FROM porteiros";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['nome'] . '">' . $row['nome'] . '</option>';
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
                <label for="veiculo">Veículo</label>
                <input type="text" id="veiculo" name="veiculo">
            </div>

            <div class="form-group">
                <label for="motorista">Motorista</label>
                <input type="text" id="motorista" name="motorista">
            </div>

            <div class="form-group km">
                <div class="km-item">
                    <label for="km_saida">KM saída</label>
                    <input type="number" id="km_saida" name="km_saida">
                </div>
                <div class="km-item">
                    <label for="km_chegada">KM chegada</label>
                    <input type="number" id="km_chegada" name="km_chegada">
                </div>
            </div>

            <div class="form-group horario">
        <div class="horario-item">
            <label for="horario_saida"><i class="fas fa-clock"></i> Horário saída</label>
            <input type="text" id="horario_saida" name="horario_saida">
        </div>
        <div class="horario-item">
            <label for="horario_entrada"><i class="fas fa-clock"></i> Horário chegada</label>
            <input type="text" id="horario_entrada" name="horario_entrada">
        </div>
    </div>

            <div class="form-group">
                <label for="destino">Destino</label>
                <input type="text" id="destino" name="destino">
            </div>

            <div class="form-group">
                <label for="motivo">Motivo</label>
                <input type="text" id="motivo" name="motivo">
            </div>

            <div class="button-container">
                <button type="submit" id="submit-btn" class="styled">Salvar</button>
            </div>
        </form>
    </div>

        <!-- Contêiner para o launcher -->
        <div id="launcher" class="launcher hidden">
            <div class="launcher-message">Cadastro editado com sucesso!</div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
