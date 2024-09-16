let currentPage = 1;
let rowsPerPage = parseInt(localStorage.getItem('rowsPerPage')) || parseInt(document.getElementById('rows-per-page').value); // Recupera o valor armazenado ou o valor padrão
const table = document.getElementById('setor-list');
let tableRows = Array.from(table.querySelectorAll('tr')); // Convert to array for easier manipulation
let totalRows = tableRows.length;
let totalPages = Math.ceil(totalRows / rowsPerPage);

function updateRows() {
    rowsPerPage = parseInt(document.getElementById('rows-per-page').value);
    localStorage.setItem('rowsPerPage', rowsPerPage); // Armazena o valor no localStorage
    totalRows = tableRows.length;
    totalPages = Math.ceil(totalRows / rowsPerPage);
    currentPage = 1; // Reset to the first page when changing the number of rows per page
    renderTable();
}

// Inicializar os botões de excluir
function initializeDeleteButtons() {
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            openDeleteModal(this.dataset.id);
        });
    });
}

function renderTable() {
    // Ocultar todas as linhas
    tableRows.forEach((row, index) => {
        row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? '' : 'none';
    });


    // Atualizar o estado dos botões de paginação
    document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('next-page').classList.toggle('disabled', currentPage === totalPages);
    document.getElementById('first-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('last-page').classList.toggle('disabled', currentPage === totalPages);

    // Atualizar links de página
    const pageLinks = document.getElementById('page-links');
    pageLinks.innerHTML = '';

    // Calcular o intervalo de páginas a ser exibido
    const numLinks = 3;
    let startPage = Math.max(1, currentPage - Math.floor(numLinks / 2));
    let endPage = Math.min(totalPages, startPage + numLinks - 1);

    if (endPage - startPage + 1 < numLinks) {
        startPage = Math.max(1, endPage - numLinks + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const link = document.createElement('a');
        link.textContent = i;
        link.href = '#';
        link.className = (i === currentPage) ? 'active' : '';
        link.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            renderTable();
        });
        pageLinks.appendChild(link);
    }

    // Inicializar os botões de excluir
    initializeDeleteButtons();
}

// Event listeners for pagination buttons
document.getElementById('prev-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
});

document.getElementById('next-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
});

document.getElementById('first-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage = 1;
        renderTable();
    }
});

document.getElementById('last-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage < totalPages) {
        currentPage = totalPages;
        renderTable();
    }
});


// Inicializar a tabela e botões ao carregar a página
window.onload = function() {
    // Configura o valor inicial do seletor de linhas por página
    const rowsPerPageSelector = document.getElementById('rows-per-page');
    rowsPerPageSelector.value = rowsPerPage;
    renderTable();
    initializeDeleteButtons();
};


document.getElementById('rows-per-page').addEventListener('change', updateRows);

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('salvar-setor').addEventListener('click', function(event) {
        event.preventDefault(); // Prevenir o envio padrão do formulário

        var nomeField = document.getElementById('nome');
        var nome = nomeField.value.trim(); // Obter o valor do campo de nome e remover espaços extras

        // Verificar se o campo está vazio
        if (nome === '') {
            // Mostrar mensagem de erro
            nomeField.classList.add('error');
            showLauncher('O campo Nome do Porteiro não pode estar vazio.', true); // Passar true para mostrar erro
            return; // Interrompe a execução do restante do código
        } else {
            // Remove a classe de erro se o campo não estiver vazio
            nomeField.classList.remove('error');
        }

        // Realizar a requisição AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'config/porteiros_config.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Criar a nova linha da tabela
                    var newRow = document.createElement('tr');
                    newRow.setAttribute('data-id', response.id);
                    newRow.innerHTML = '<td>' + response.nome + '</td><td><button class="delete-button" data-id="' + response.id + '">Excluir</button></td>';

                    // Adicionar a nova linha no início da tabela
                    document.getElementById('setor-list').insertAdjacentElement('afterbegin', newRow);

                    // Recalcular linhas e total de páginas após adicionar o novo registro
                    tableRows = Array.from(document.getElementById('setor-list').querySelectorAll('tr')); // Recarregar a lista de linhas
                    totalRows = tableRows.length;
                    totalPages = Math.ceil(totalRows / rowsPerPage);

                    // Ir para a primeira página
                    currentPage = 1;
                    renderTable(); // Re-renderizar a tabela

                    // Limpar o campo de nome
                    nomeField.value = '';

                    // Mostrar o launcher de notificação
                    showLauncher('Cadastro salvo com sucesso!');
                } else {
                    showLauncher(response.message, true); // Passar true para mostrar erro
                }
            } else {
                showLauncher('Erro ao realizar a requisição.', true); // Passar true para mostrar erro
            }
        };

        xhr.send('nome=' + encodeURIComponent(nome));
    });
        // Inicializar os botões de excluir na carga inicial da página
        initializeDeleteButtons();
});


// Função para mostrar o launcher de notificação
function showLauncher(message, isError = false) {
    const launcher = document.getElementById('launcher');
    const launcherMessage = document.querySelector('.launcher-message');
    
    // Atualiza a mensagem do launcher
    launcherMessage.textContent = message;

    // Adiciona ou remove a classe de erro com base no parâmetro
    if (isError) {
        launcher.classList.add('launcher-error');
    } else {
        launcher.classList.remove('launcher-error');
    }

    // Mostra o launcher com animação
    launcher.classList.remove('hidden');
    launcher.classList.add('launcher-show');

    // Oculta o launcher após 2 segundos
    setTimeout(() => {
        launcher.classList.remove('launcher-show');
        launcher.classList.add('hidden');
    }, 2000);
}


// Abre o modal de confirmação
function openDeleteModal(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('confirmacao').value = ''; // Limpa o campo de confirmação
    document.getElementById('modal-delete').style.display = 'flex';
}

// Fecha o modal de confirmação
function closeModal() {
    document.getElementById('modal-delete').style.display = 'none';
}

// Valida e envia a solicitação de exclusão
function submitDeleteForm(event) {
    // Evita o comportamento padrão do formulário
    if (event) event.preventDefault();

    const confirmacao = document.getElementById('confirmacao').value.toLowerCase();
    const id = document.getElementById('deleteId').value;

    if (confirmacao !== 'excluir') {
        showLauncher('Por favor, digite "excluir" para confirmar.', true);
        return false;
    }

    // Envia a requisição AJAX para excluir o setor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'config/porteiros_config.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Encontra e remove a linha correspondente ao ID
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                row.remove(); // Remove a linha da tabela
            }

            // Recalcular total de linhas e páginas
            tableRows = Array.from(table.querySelectorAll('tr')); // Recarregar a lista de linhas
            totalRows = tableRows.length;
            totalPages = Math.ceil(totalRows / rowsPerPage);

            // Atualizar a tabela e a paginação
            renderTable();

            showLauncher('Cadastro excluído com sucesso!');
        } else {
            showLauncher('Erro ao excluir o cadastro. Status: ' + xhr.status, true);
        }
    };

    xhr.onerror = function() {
        showLauncher('Erro ao realizar a requisição.', true);
    };

    xhr.send('id=' + encodeURIComponent(id));

    closeModal(); // Fecha o modal após a tentativa de exclusão
}

// Adiciona evento para o botão de cancelar fechar o modal
document.querySelector('.modal-content .cancel').addEventListener('click', closeModal);

// Adiciona evento ao formulário para enviar ao pressionar Enter
document.getElementById('deleteForm').addEventListener('submit', submitDeleteForm);

// Adiciona evento para enviar o formulário ao pressionar Enter no campo de confirmação
document.getElementById('confirmacao').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        submitDeleteForm(event);
    }
});




