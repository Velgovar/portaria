<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: logine.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Porteiro/Vigia</title>
    <link rel="stylesheet" href="../css/porteiros.css">
    <style>
        .container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            max-width: 1200px;
            height: 400px;
            max-height: 80vh;
            overflow-y: auto; /* Adicionar barra de rolagem vertical apenas quando necessário */
            padding: 20px;
            background-color: rgba(51, 51, 51, 0.9);
            border-radius: 4%;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                        5px 5px 20px rgba(0, 0, 0, 0.2),
                        10px 10px 25px rgba(0, 0, 0, 0.1);
        }

        .container::-webkit-scrollbar {
          display: none; /* Esconder a barra de rolagem do Chrome/Safari */
        }

        .message {
            display: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 300px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Estilo do Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #333;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 80%;
            position: relative; /* Para posicionar os botões no modal */
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content p {
            margin-bottom: 20px;
        }

        .modal-content input[type=text] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: calc(100% - 22px);
            font-size: 14px;
            box-sizing: border-box;
        }

        .modal-content button {
            margin: 5px 10px;
            padding: 10px 16px;
            border: none;
            background-color: #f44336;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .modal-content button:hover {
            background-color: #cc0000;
        }

        .modal-content button.cancel {
            background-color: #ccc;
            color: black;
        }

        .modal-content button.cancel:hover {
            background-color: #999;
        }

        /* Estilo do botão verde de voltar */
        .style {
            position: absolute;
            top: 10px;
            left: 10px;
            border: 0;
            color: white;
            line-height: 2.5;
            padding: 0 70px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
            border-radius: 10px;
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

        /* Estilo do botão vermelho de exclusão */
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
<a href="../login/entrar.php" class="style">Voltar</a>

    <div class="container">
        <h2>Cadastro de Porteiro/Vigia</h2>

        <form id="vigia-form" method="POST" action="../config/salvar_porteiro.php">
            <div class="form-group">
                <label for="nome">Nome do Vigia:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="button-container">
                <button type="submit" class="styled">Salvar Vigia</button>
            </div>
        </form>

        <hr> <!-- Linha separadora -->

        <h2>Lista de Vigias Cadastrados</h2>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="vigia-list">
                <?php
                // Conexão com o banco de dados
                $servername = "192.168.254.136";
                $username = "felipe";
                $password = "Aranhas12@";
                $dbname = "cobra";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                }

                // Query para selecionar todos os vigias
                $sql = "SELECT id, nome FROM porteiros";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Exibe os vigias em uma tabela
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr data-id="' . $row['id'] . '">';
                        echo '<td>' . $row['nome'] . '</td>';
                        echo '<td><button class="delete-btn" data-id="' . $row['id'] . '">Excluir</button></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">Nenhum vigia cadastrado.</td></tr>';
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Mensagem de feedback para cadastro -->
    <div id="cadastroMessage" class="message"></div>

    <!-- Mensagem de feedback para exclusão -->
    <div id="deleteMessage" class="message"></div>

   <!-- Modal de Confirmação de Exclusão -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h3>Confirmação de Exclusão</h3>
            <p>Digite <strong>excluir</strong> abaixo para confirmar a exclusão</p>
            <form id="deleteForm" action="../config/excluir_porteiro.php" method="post">
                <input type="hidden" name="id" id="deleteId">
                <input type="text" name="confirmacao" id="confirmacao" placeholder="" autocomplete="off">
                <button type="submit" onclick="return validateConfirmation()">Sim, Excluir</button>
                <button type="button" class="cancel">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Função para exibir mensagem de feedback
            function showMessage(message, type, targetElement) {
                var messageDiv = $('#' + targetElement);
                messageDiv.removeClass('success error').addClass(type);
                messageDiv.text(message).show();
                setTimeout(function() {
                    messageDiv.hide();
                }, 5000);
            }

            // Evento de envio do formulário de cadastro
            $('#vigia-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '../config/salvar_porteiro.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showMessage(response.message, 'success', 'cadastroMessage');
                            $('#vigia-list').append('<tr data-id="' + response.id + '"><td>' + $('#nome').val() + '</td><td><button class="delete-btn" data-id="' + response.id + '">Excluir</button></td></tr>');
                            $('#nome').val('');
                        } else {
                            showMessage(response.message, 'error', 'cadastroMessage');
                        }
                    },
                    error: function() {
                        showMessage('Erro ao salvar o vigia. Por favor, tente novamente mais tarde.', 'error', 'cadastroMessage');
                    }
                });
            });

            // Evento de clique no botão de exclusão
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();

                var id = $(this).data('id');

                // Setando o ID a ser excluído no modal de confirmação
                $('#deleteId').val(id);

                // Exibindo o modal
                $('#myModal').show();
            });

            // Evento de clique no botão cancelar do modal
            $('.modal-content button.cancel').on('click', function() {
                $('#myModal').hide();
                $('#confirmacao').val(''); // Limpar o campo de confirmação ao fechar o modal
            });

            // Evento de envio do formulário de confirmação de exclusão
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();

                var id = $('#deleteId').val();
                var confirmacao = $('#confirmacao').val().trim().toLowerCase();

                if (confirmacao === 'excluir') {
                    $.ajax({
                        type: 'POST',
                        url: '../config/excluir_porteiro.php',
                        data: { id: id },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                showMessage(response.message, 'success', 'deleteMessage');
                                $('#myModal').hide();
                                $('#vigia-list tr[data-id="' + id + '"]').remove();
                            } else {
                                showMessage(response.message, 'error', 'deleteMessage');
                            }
                        },
                        error: function() {
                            showMessage('Erro ao excluir o vigia. Por favor, tente novamente mais tarde.', 'error', 'deleteMessage');
                        }
                    });
                } else {
                    showMessage('Confirmação inválida. Digite "excluir" para confirmar a exclusão.', 'error', 'deleteMessage');
                }
            });
        });
    </script>
</body>
</html>
