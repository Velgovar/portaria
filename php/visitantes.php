<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Veículos</title>
    <link rel="stylesheet" href="../css/veiculos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .style {
    position: absolute;
    top: 10px;
    left: 10px;
    border: 0;
    color: white;
    line-height: 1.75; /* Ajustado para um pouco mais de altura */
    padding: 0 40px; /* Aumentado um pouco */
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.95rem; /* Ajustado para um pouco maior */
    border-radius: 10px; /* Ajustado para um pouco maior */
    cursor: pointer;
    background-color: rgb(0, 138, 143);
    background-image: linear-gradient(
      to top left,
        rgb(0 0 0 / 20%),
        rgb(0 0 0 / 20%) 30%,
        rgb(0 0 0 / 0%));
    box-shadow:
      inset 2px 2px 3px rgb(255 255 255 / 60%),
      inset -2px -2px 3px rgb(0 0 0 / 60%);
}

.style:hover {
    background-color: rgb(0, 209, 216);
}

.style:active {
    box-shadow:
      inset -2px -2px 3px rgb(255 255 255 / 60%),
      inset 2px 2px 3px rgb(0 0 0 / 60%);
}


    
    </style>
</head>
<body>
    <a href="../menu.html" class="style">Voltar</a>

    <div class="container">
        <img src="../images/inusitta.png" class="image-center" alt="icone central">
        <h2>Controle de Veículos da Empresa</h2>
        <form id="vehicle-form" method="POST" action="../config/configr.php" autocomplete="off">
            <div class="form-group data-porteiro">
                <div class="data-item">
                    <label for="date"><i class="fas fa-calendar-alt"></i> Data:</label>
                    <input type="date" id="date" name="data">
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
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome">
            </div>

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf">
            </div>

            <div class="form-group">
                <label for="tipovisitante">Tipo de visitante</label>
                <select type="text" id="tipovisitante" name="tipovisitante">
                    <option value="">Selecione...</option>
                    <option value="Candidato">Candidato</option>
                    <option value="Cliente">Cliente</option>
                    <option value="Fornecedor">Fornecedor</option>
                    <option value="Acabamento">Prestador de serviço</option>

                </select>
            </div>

            <div class="form-group">
                <label for="servico">Qual serviço prestou?</label>
                <input type="text" id="servico" name="servico">
            </div>

            <div class="form-group">
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa">
            </div>


            <div class="form-group km">
                <div class="km-item">
                    <label for="estacionamento">Usou estacionamento?</label>
                    <select type="text" id="estacionamento" name="estacionamento">
                    <option value="">Selecione...</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                    </select>
                </div>
                <div class="km-item" style="display: none;">
                    <label for="placa">Qual placa?</label>
                    <input type="text" id="placa" name="placa">
                </div>
            </div>

            <div class="form-group horario">
                <div class="horario-item">
                    <label for="horario_entrada"><i class="fas fa-clock"></i> Horário entrada</label>
                    <input type="time" id="horario_entrada" name="horario_entrada">
                </div>
                <div class="horario-item">
                    <label for="horario_saida"><i class="fas fa-clock"></i> Horário saida</label>
                    <input type="time" id="horario_saida" name="horario_saida">
                </div>
            </div>

            <div class="form-group km">
                <div class="km-item">
                    <label for="colaborador">Colaborador responsavel</label>
                    <input type="text" id="colaborador" name="colaborador">
                </div>
                <div class="km-item">
                    <label for="setor">Setor</label>
                    <select type="text" id="setor" name="setor">
                    <option value="">Selecione um setor</option>
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

                        // Query para buscar os setores cadastrados
                        $sql = "SELECT id, nome FROM setores";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['nome'] . '">' . $row['nome'] . '</option>';
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

            <!-- Mensagem de erro -->
            <div id="error-message" class="error-message"></div>

            <!-- Mensagem de sucesso -->
            <div id="success-message" class="success-message"></div>
        </form>
    </div>

    <script src="../js/visitantes.js"></script>
</body>
</html>
