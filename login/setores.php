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
    <title>Cadastro de Setores</title>
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
            display: none; /* Oculta o modal por padrão */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.3);
    justify-content: center; /* Centraliza horizontalmente */
    align-items: center;    /* Centraliza verticalmente */
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
    border: 1px solid #666;
    border-radius: 4px;
    width: calc(100% - 22px);
    font-size: 14px;
    background-color: #222;
    color: white;
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

        .main-container {
    display: flex; /* Usar flexbox para posicionar o formulário e a tabela lado a lado */
    justify-content: space-between; /* Espaço entre o formulário e a tabela */
    width: 100%;
    max-width: 1000px; /* Ajuste conforme necessário */
    margin: 0 auto;
    padding: 20px;
    background-color: rgba(51, 51, 51, 0.9);
    border-radius: 4%;
    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3),
                5px 5px 20px rgba(0, 0, 0, 0.2),
                10px 10px 25px rgba(0, 0, 0, 0.1);
}

.fixed-container {
    background-color: rgba(51, 51, 51, 0.9);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    width: 30%; /* Ajuste a largura conforme necessário */
    margin-right: 20px; /* Espaço entre o formulário e a tabela */
    box-sizing: border-box; /* Inclui padding e borda na largura total */
}

.fixed-scrollable-container {
    width: 70%; /* Ajuste a largura conforme necessário */
    overflow-x: auto; /* Habilita o scroll horizontal se necessário */
    position: relative; /* Necessário para o posicionamento do cabeçalho fixo */
}

.scrollable-container {
    max-width: 100%;
    height: 400px; /* Ajuste a altura para caber aproximadamente 10 linhas */
    overflow-y: auto; /* Habilita o scroll vertical para o conteúdo excedente */
    margin: 0; /* Remove a margem para evitar espaço adicional */
    position: relative; /* Necessário para o posicionamento do cabeçalho fixo */
}

.scrollable-container h2 {
    position: -webkit-sticky; /* Para navegadores WebKit */
    position: sticky; /* Para navegadores modernos */
    top: 0; /* Fica fixo no topo do contêiner de rolagem */
    background-color: rgba(51, 51, 51, 0.9); /* Cor de fundo para o título */
    padding: 10px; /* Espaço ao redor do título */
    margin: 0; /* Remove a margem para um alinhamento mais preciso */
    z-index: 2; /* Garante que o título fique sobre o conteúdo da tabela */
}

.scrollable-container table {
    width: 100%;
    border-collapse: collapse; /* Remove os espaços entre as bordas da tabela */
    margin-top: 10px; /* Espaço entre o título e a tabela */
}

.scrollable-container th, .scrollable-container td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd; /* Linha horizontal abaixo de cada célula */
    border-right: 1px solid #ddd; /* Linha vertical à direita de cada célula */
}

.scrollable-container th {
    background-color: #333; /* Cor de fundo do cabeçalho */
    color: white;
    border-top: 1px solid #ddd; /* Linha superior */
    position: -webkit-sticky; /* Para navegadores WebKit */
    position: sticky; /* Para navegadores modernos */
    top: 40px; /* Ajuste conforme necessário para o tamanho do título */
    z-index: 1; /* Garante que o cabeçalho fique sobre o conteúdo da tabela */
}

.scrollable-container td:last-child,
.scrollable-container th:last-child {
    border-right: none; /* Remove a linha da última coluna para evitar borda dupla */
}

/* Diminuir o campo de ações */
.scrollable-container td:nth-child(2),
.scrollable-container th:nth-child(2) {
    width: 80px; /* Define a largura da coluna de ações */
    text-align: center; /* Centraliza o conteúdo da coluna de ações */
}



    </style>
</head>
<body>
<a href="../login/entrar.php" class="style">Voltar</a>


<div class="main-container">
    <!-- Container fixo para o formulário -->
    <div class="fixed-container">
        <h2>Cadastro de Setores</h2>
        <form id="vigia-form" method="POST" action="../config/salvar_porteiro.php">
            <div class="form-group">
                <label for="nome">Nome do Setor</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="button-container">
                <button type="submit" class="styled">Salvar Setor</button>
            </div>
        </form>
    </div>

    <!-- Container fixo para a tabela -->
    <div class="fixed-scrollable-container">
        <!-- Tabela com a lista de setores cadastrados -->
        <div class="scrollable-container">
            <h2>Lista de Setores Cadastrados</h2>
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

                    // Query para selecionar todos os Setores
                    $sql = "SELECT id, nome FROM setores";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Exibe os setores em uma tabela
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr data-id="' . $row['id'] . '">';
                            echo '<td>' . $row['nome'] . '</td>';
                            echo '<td><button class="delete-btn" data-id="' . $row['id'] . '">Excluir</button></td>';
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
    </div>
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
                        showMessage('Erro ao salvar o Setor. Por favor, tente novamente mais tarde.', 'error', 'cadastroMessage');
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
                            showMessage('Erro ao excluir o Setor. Por favor, tente novamente mais tarde.', 'error', 'deleteMessage');
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
