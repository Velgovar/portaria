<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de apresentação</title>
    <link rel="icon" href="../images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="css/apresentacao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

                </head>
            <body>

                <a href="../menu.html" class="voltar">Voltar</a>

            <div class="container">
                <h2>Controle de apresentação</h2>
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
            
            <div class="form-group data-porteiro">
                <div class="data-item">
                    <label for="horario_apresentacao">Hora da apesentação</label>
                    <div class="input-icon">
                        <i class="fas fa-clock"></i> 
                        <input type="text" id="horario_apresentacao" name="horario_apresentacao" placeholder="Ex: 12:00">
                </div>
            </div>

            <div class="porteiro-item">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Carlos Eduardo Santos">
                </div>
            </div>

            <div class="form-group">
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa" placeholder="Ex: Inusittá">
            </div>

            <div class="button-container">
                <button type="submit" id="submit-btn" class="salvar">Salvar</button>
            </div>
        </form>
    </div>

        <div id="launcher" class="launcher hidden">
            <div class="launcher-message">Cadastro editado com sucesso!</div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
        <script src="js/apresentacao.js"></script>

    </body>
</html>
