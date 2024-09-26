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
        <title>Cadastrar Setores</title>
        <link rel="icon" href="../images/favicon.ico" type="image/png">
       <link rel="stylesheet" href="css/setores.css">
    </head>
        <body>
            <div class="setor-container">
                <div class="fixed-container">
                <form id="vigia-form" method="POST" action="config/setores_config.php">
                    <div class="form-group">
                    <div class="form-group">
                    <h2>Cadastrar Setores</h2>
                        <input type="text" id="nome" name="nome" placeholder="Digite o nome do Setor" required>
                        <div id="nome-error" class="error-message"></div> 
                    </div>
                    </div>
                    <div class="button-container">
                        <button type="submit" id="salvar-setor" class="salvar-button">Salvar</button>
                    </div>
                </form>
            </div>

            <a href="menu.php" class="voltar">Voltar</a>

            <div class="table-container">
                <div class="table-header">
                    <h2>Lista de Setores Cadastrados</h2>
                    <thead>
                </div>
                <div class="scrollable-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
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

                            $sql = "SELECT id, nome FROM setores ORDER BY id DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr data-id="' . $row['id'] . '">';
                            echo '<td>' . $row['nome'] . '</td>';
                            echo '<td><button class="delete-button" data-id="' . $row['id'] . '">Excluir</button></td>';
                            echo '</tr>';
                        }
                            } else {
                        echo '<tr><td colspan="2">Nenhum Setor cadastrado.</td></tr>';
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

            <div id="modal-delete" class="modal">
                <div class="modal-content">
                    <h3>Confirmação de Exclusão</h3>
                    <p>Você tem certeza que deseja excluir este cadastro?</p>
                    <form id="deleteForm">
                        <input type="hidden" name="id" id="deleteId">
                        <input type="text" name="confirmacao" id="confirmacao" placeholder="Digite 'excluir' para confirmar" autocomplete="off">

                        <button type="button" class="delete-button" onclick="submitDeleteForm()">Excluir</button>

                        <button type="button" class="cancel">Cancelar</button>
                    </form>
                </div>
            </div>
 
        <div id="launcher" class="launcher hidden">
            <div class="launcher-message">Cadastro editado com sucesso!</div>
        </div>

        <script src="js/setores.js"></script>

    </body>
</html>