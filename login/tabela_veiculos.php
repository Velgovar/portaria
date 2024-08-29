<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: logine.php');
    exit;
}

// Configurações de conexão com o banco de dados
$host = '192.168.254.136';
$dbname = 'cobra';
$username = 'felipe';
$password = 'Aranhas12@';

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Definir o número de registros por página
    $registrosPorPagina = isset($_GET['registrosPorPagina']) ? (int)$_GET['registrosPorPagina'] : 10;

    // Capturar o número da página atual
    $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($paginaAtual - 1) * $registrosPorPagina;

    // Capturar os critérios de busca
    $criterio = isset($_GET['criterio']) ? $_GET['criterio'] : 'id';
    $busca = isset($_GET['busca']) ? $_GET['busca'] : '';

    // Validar o critério de busca
    $criteriosValidos = ['id', 'data', 'porteiro', 'veiculo', 'km_saida', 'km_chegada', 'horario_saida', 'horario_chegada', 'destino', 'motivo', 'acao'];
    if (!in_array($criterio, $criteriosValidos)) {
        $criterio = 'id';
    }

    // Consultar o total de registros com base no critério de busca
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registros_veiculos WHERE $criterio LIKE :busca");
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $stmt->execute();
    $totalRegistros = $stmt->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    // Consultar os registros com base na página atual e critério de busca
    $stmt = $pdo->prepare("SELECT * FROM registros_veiculos WHERE $criterio LIKE :busca ORDER BY id DESC LIMIT :offset, :limit");
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $registrosPorPagina, PDO::PARAM_INT);
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

// Verificar se há mensagem de sucesso
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Veículos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <a href="../login/entrar.php" class="style">Voltar</a>

    <div class="container_table1">
        <div class="header">
            <h2>[Administrador] Cadastros de Veiculos</h2>
        </div>
        <!-- Formulário de Busca -->
        <form class="search-form" method="get" action="">
    <label for="campo-busca">Buscar por</label>
    <select name="criterio" id="criterio">
        <option value="id">ID</option>
        <option value="data">DATA</option>
        <option value="porteiro">PORTEIRO</option>
        <option value="veiculo">VEICULO</option>
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
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DATA</th>
                        <th>PORTEIRO</th>
                        <th>VEICULO</th>
                        <th>KM SAIDA</th>
                        <th>KM CHEGADA</th>
                        <th>HORARIO SAIDA</th>
                        <th>HORARIO CHEGADA</th>
                        <th>DESTINO</th>
                        <th>MOTIVO</th>
                        <th>AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr data-id="<?php echo htmlspecialchars($registro['id']); ?>">
                            <td><?php echo htmlspecialchars($registro['id']); ?></td>
                            <td><?php echo htmlspecialchars($registro['data']); ?></td>
                            <td><?php echo htmlspecialchars($registro['porteiro']); ?></td>
                            <td><?php echo htmlspecialchars($registro['veiculo']); ?></td>
                            <td><?php echo htmlspecialchars($registro['km_saida']); ?></td>
                            <td class="km_chegada"><?php echo htmlspecialchars($registro['km_chegada']); ?></td>
                            <td class="horario_saida"><?php echo htmlspecialchars($registro['horario_saida']); ?></td>
                            <td class="horario_chegada"><?php echo htmlspecialchars($registro['horario_chegada']); ?></td>
                            <td><?php echo htmlspecialchars($registro['destino']); ?></td>
                            <td><?php echo htmlspecialchars($registro['motivo']); ?></td>
                            <td>
    <button class="edit-button" onclick="openModal(
        <?php echo htmlspecialchars($registro['id']); ?>, 
        '<?php echo htmlspecialchars($registro['km_chegada']); ?>', 
        '<?php echo htmlspecialchars($registro['horario_chegada']); ?>'
    )">Editar</button>
                    <button class="delete-button" onclick="openConfirmationModal(<?php echo htmlspecialchars($registro['id']); ?>)">Excluir</button>
                    </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination-container">
                <div class="select-container">
                    <select id="registrosPorPagina" onchange="changeRecordsPerPage()">
                        <option value="10" <?php echo $registrosPorPagina == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo $registrosPorPagina == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo $registrosPorPagina == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $registrosPorPagina == 100 ? 'selected' : ''; ?>>100</option>
                        <option value="1000" <?php echo $registrosPorPagina == 1000 ? 'selected' : ''; ?>>1000</option>
                        <option value="10000" <?php echo $registrosPorPagina == 10000 ? 'selected' : ''; ?>>10000</option>
                        <option value="100000" <?php echo $registrosPorPagina == 100000 ? 'selected' : ''; ?>>100000</option>
                        <option value="1000000" <?php echo $registrosPorPagina == 1000000 ? 'selected' : ''; ?>>1000000</option>

                    </select>
                    <span>Linhas / Paginas</span>
                </div>
                <div class="pagination">
                    <a href="?pagina=1&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == 1) echo 'class="disabled"'; ?>>&laquo;</a>
                    <a href="?pagina=<?php echo max(1, $paginaAtual - 1); ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == 1) echo 'class="disabled"'; ?>>&lt;</a>
                    
                    <?php
                    $numLinks = 3;
                    $startPage = max(1, $paginaAtual - floor($numLinks / 2));
                    $endPage = min($totalPaginas, $startPage + $numLinks - 1);
                    
                    if ($endPage - $startPage + 1 < $numLinks) {
                        $startPage = max(1, $endPage - $numLinks + 1);
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="?pagina=<?php echo $i; ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($i == $paginaAtual) echo 'class="active"'; ?>><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <a href="?pagina=<?php echo min($totalPaginas, $paginaAtual + 1); ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == $totalPaginas) echo 'class="disabled"'; ?>>&gt;</a>
                    <a href="?pagina=<?php echo $totalPaginas; ?>&registrosPorPagina=<?php echo $registrosPorPagina; ?>" <?php if ($paginaAtual == $totalPaginas) echo 'class="disabled"'; ?>>&raquo;</a>
                </div>
            </div>

            <div class="cancel-message" id="cancelMessage"></div>

            <?php if ($mensagemSucesso): ?>
                <div class="success-message"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
            <?php endif; ?>
        </div>

<!-- Modal de confirmação -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <h3>Confirmação de Exclusão</h3>
        <p>Você tem certeza que deseja excluir este cadastro?</p>
        <form id="confirmationForm">
<!-- Campo de texto para confirmação -->
<input type="text" id="confirmationInput" autocomplete="off" placeholder="Digite 'excluir' para confirmar">
            <button type="submit">Excluir</button>
            <button type="button" class="cancel" onclick="closeConfirmationModal()">Cancelar</button>
        </form>
    </div>
</div>

<!-- Container para mensagens -->
<div class="message-container"></div>




        <div class="modal" id="editModal">
    <div class="modal-content">
        <h2>Editar Registro</h2>
        <form id="editForm" action="../config/editar_table_veiculos.php" method="POST">
            <input type="hidden" id="editId" name="id">
            <label for="editData">DATA:</label>
            <input type="text" id="editData" name="data" required autocomplete="off">
            <label for="editPorteiro">PORTEIRO:</label>
            <input type="text" id="editPorteiro" name="porteiro" required autocomplete="off">
            <label for="editVeiculo">VEICULO:</label>
            <input type="text" id="editVeiculo" name="veiculo" required autocomplete="off">
            <label for="editKmSaida">KM SAIDA:</label>
            <input type="text" id="editKmSaida" name="km_saida" required autocomplete="off">
            <label for="editKmChegada">KM Chegada:</label>
            <input type="text" id="editKmChegada" name="km_chegada" required autocomplete="off">
            <label for="editHorarioSaida">Horário Saída:</label>
            <input type="time" id="editHorarioSaida" name="horario_saida" required autocomplete="off">            
            <label for="editHorarioChegada">Horário Chegada:</label>
            <input type="time" id="editHorarioChegada" name="horario_chegada" required autocomplete="off">
            <label for="editDestino">DESTINO:</label>
            <input type="text" id="editDestino" name="destino" required autocomplete="off">
            <label for="editMotivo">MOTIVO:</label>
            <input type="text" id="editMotivo" name="motivo" required autocomplete="off">
            <button type="submit" class="save-button">Salvar</button>
            <button type="button" class="close" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<!-- Contêiner para o launcher -->
<div id="launcher" class="launcher hidden">
    <div class="launcher-message">Cadastro editado com sucesso!</div>
</div>

<!-- Contêiner para o launcher de exclusão -->
<div id="launcherDelete" class="launcher-delete hidden">
    <div class="launcher-delete-message">Item excluído com sucesso!</div>
</div>




        <div class="message-container"></div>

        <script>
// Função para abrir o modal e preencher os campos com os dados mais recentes da linha
function openModal(id) {
    // Seleciona a linha correspondente pelo ID
    const row = document.querySelector(`tr[data-id="${id}"]`);
    
    if (row) {
    // Pegue os valores mais recentes da linha da tabela
    const km_chegada = row.querySelector('.km_chegada').textContent.trim();
    const horario_chegada = row.querySelector('.horario_chegada').textContent.trim();
    const horario_saida = row.querySelector('td:nth-child(7)').textContent.trim(); // Coluna horario saida
    const data = row.querySelector('td:nth-child(2)').textContent.trim(); // Coluna DATA
    const porteiro = row.querySelector('td:nth-child(3)').textContent.trim(); // Coluna PORTEIRO
    const veiculo = row.querySelector('td:nth-child(4)').textContent.trim(); // Coluna VEICULO
    const km_saida = row.querySelector('td:nth-child(5)').textContent.trim(); // Coluna KM SAIDA
    const destino = row.querySelector('td:nth-child(9)').textContent.trim(); // Coluna DESTINO
    const motivo = row.querySelector('td:nth-child(10)').textContent.trim(); // Coluna MOTIVO

    // Preenche os campos do modal com os valores mais recentes
    document.getElementById('editId').value = id;
    document.getElementById('editKmChegada').value = km_chegada;
    document.getElementById('editHorarioChegada').value = horario_chegada;
    document.getElementById('editHorarioSaida').value = horario_saida;
    document.getElementById('editData').value = data;
    document.getElementById('editPorteiro').value = porteiro;
    document.getElementById('editVeiculo').value = veiculo;
    document.getElementById('editKmSaida').value = km_saida;
    document.getElementById('editDestino').value = destino;
    document.getElementById('editMotivo').value = motivo;

    // Abra o modal
    document.getElementById('editModal').style.display = 'flex';
} else {
    console.error(`Linha com ID ${id} não encontrada.`);
}

}

// Variável global para armazenar o ID do item a ser excluído
let deleteId = null;

// Função para abrir o modal de confirmação
function openConfirmationModal(id) {
    deleteId = id; // Define o ID do item a ser excluído
    document.getElementById('confirmationModal').style.display = 'flex';
}

// Função para fechar o modal de confirmação
function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('confirmationInput').value = ''; // Limpa o campo de texto
}

// Função para confirmar a exclusão
function confirmDelete() {
    const confirmationInput = document.getElementById('confirmationInput').value.trim().toLowerCase();
    if (confirmationInput === 'excluir') {
        fetch('../config/editar_table_veiculos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': deleteId,
                'confirmacao': 'excluir'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove a linha da tabela
                const row = document.querySelector(`tr[data-id="${deleteId}"]`);
                if (row) {
                    row.remove();
                }

                // Mostra o launcher de notificação
                const deleteLauncher = document.getElementById('launcherDelete');
                deleteLauncher.classList.remove('hidden');
                deleteLauncher.classList.add('visible');

                // Remove o launcher após 2 segundos
                setTimeout(() => {
                    deleteLauncher.classList.remove('visible');
                    deleteLauncher.classList.add('hidden');
                }, 2000);

                closeConfirmationModal(); // Fecha o modal após a confirmação
            } else {
                // Mostra a mensagem de erro
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            closeConfirmationModal(); // Fecha o modal mesmo se ocorrer um erro
        });
    } else {
        alert('Você deve digitar "excluir" para confirmar.');
    }
}


// Adiciona um listener para o envio do formulário
document.getElementById('confirmationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne o envio padrão do formulário
    confirmDelete(); // Chama a função de confirmação
});


// Função para fechar o modal e exibir a mensagem de cancelamento (se necessário)
function closeModal(showCancelMessage = false) {
    document.getElementById('editModal').style.display = 'none';
    
    if (showCancelMessage) {
        // Adicionar a mensagem de cancelamento
        const cancelMessage = document.getElementById('cancelMessage');
        cancelMessage.textContent = 'Edição cancelada.';
        cancelMessage.style.display = 'block';

        // Remover a mensagem após 2 segundos
        setTimeout(() => {
            cancelMessage.style.display = 'none';
        }, 2000);
    }
}

// Função para alterar o número de registros por página
function changeRecordsPerPage() {
    const select = document.getElementById('registrosPorPagina');
    const registrosPorPagina = select.value;
    window.location.href = `?pagina=1&registrosPorPagina=${registrosPorPagina}`;
}

function handleFormSubmit(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    // Mostra o launcher de notificação
    const launcher = document.getElementById('launcher');
    launcher.classList.remove('hidden');
    launcher.classList.add('visible');

    // Envia o formulário via AJAX
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    fetch('../config/editar_table_veiculos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Espera JSON como resposta
    .then(data => {
        console.log('Dados recebidos do servidor:', data); // Adiciona log para depuração

        if (data.success) {
            // Fechar o modal
            closeModal();

            // Atualizar a linha da tabela com os novos dados
            updateTableRow(
                formData.get('id'),
                formData.get('km_chegada'),
                formData.get('horario_saida'),
                formData.get('horario_chegada'),
                formData.get('data'),
                formData.get('porteiro'),
                formData.get('veiculo'),
                formData.get('km_saida'),
                formData.get('destino'),
                formData.get('motivo')
            );
        } else {
            console.error('Falha ao editar o registro:', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    })
    .finally(() => {
        // Oculta o launcher de notificação após 2 segundos
        setTimeout(() => {
            launcher.classList.remove('visible');
            launcher.classList.add('hidden');
        }, 2000); // Mantém o launcher visível por 2 segundos
    });
}


// Função para atualizar a linha da tabela com os novos dados
function updateTableRow(id, km_chegada,horario_saida, horario_chegada, data, porteiro, veiculo, km_saida, destino, motivo) {
    if (!id) {
        console.error('ID não fornecido.');
        return;
    }
    
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        console.log(`Atualizando a linha com ID ${id}`); // Log para depuração
        row.querySelector('td:nth-child(2)').textContent = data;
        row.querySelector('td:nth-child(3)').textContent = porteiro;
        row.querySelector('td:nth-child(4)').textContent = veiculo;
        row.querySelector('td:nth-child(5)').textContent = km_saida;
        row.querySelector('.km_chegada').textContent = km_chegada;
        row.querySelector('td:nth-child(7)').textContent = horario_saida;
        row.querySelector('.horario_chegada').textContent = horario_chegada;
        row.querySelector('td:nth-child(9)').textContent = destino;
        row.querySelector('td:nth-child(10)').textContent = motivo;
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}


function filterTable() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const searchSelect = document.getElementById('search-select').value;
    const table = document.getElementById('tabela-registros');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let shouldShow = false;

        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (searchSelect === '' || cell.getAttribute('data-field') === searchSelect) {
                if (cell.textContent.toLowerCase().indexOf(searchInput) > -1) {
                    shouldShow = true;
                }
            }
        }

        rows[i].style.display = shouldShow ? '' : 'none';
    }
}

// Adicionar event listener ao formulário de edição
document.getElementById('editForm').addEventListener('submit', handleFormSubmit);

// Adicionar event listener para mudança no número de registros por página
document.getElementById('registrosPorPagina').addEventListener('change', changeRecordsPerPage);

        </script>
    </div>
</body>
</html>
