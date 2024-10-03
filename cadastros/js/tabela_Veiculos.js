let currentPage = 1;
let rowsPerPage = parseInt(localStorage.getItem('rowsPerPage')) || parseInt(document.getElementById('rows-per-page').value); 
const table = document.getElementById('veiculos-list');
let tableRows = Array.from(table.querySelectorAll('tr')); 
let totalRows = tableRows.length;
let totalPages = Math.ceil(totalRows / rowsPerPage);

function updateRows() {
    rowsPerPage = parseInt(document.getElementById('rows-per-page').value);
    localStorage.setItem('rowsPerPage', rowsPerPage); 
    totalRows = tableRows.length;
    totalPages = Math.ceil(totalRows / rowsPerPage);
    currentPage = 1; 
    renderTable();
}

document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const kmChegada = this.getAttribute('data-km-chegada');
        const horarioChegada = this.getAttribute('data-horario-chegada');

        // Preenche os campos do modal com os dados do registro
        document.getElementById('editId').value = id;
        document.getElementById('editKmChegada').value = kmChegada;

        // Formata o horário de chegada, caso tenha
        const formattedHorarioChegada = horarioChegada.split(':').slice(0, 2).join(':');
        document.getElementById('editHorarioChegada').value = formattedHorarioChegada;

        // Obtém a hora atual e formata como HH:MM
        const agora = new Date();
        const horas = String(agora.getHours()).padStart(2, '0'); // Adiciona zero à esquerda
        const minutos = String(agora.getMinutes()).padStart(2, '0'); // Adiciona zero à esquerda
        const horarioAtual = `${horas}:${minutos}`;

        // Preenche o campo do horário de chegada com a hora atual
        document.getElementById('editHorarioChegada').value = horarioAtual;

        // Exibe o modal
        document.getElementById('modal-edit').style.display = 'flex';
    });
});

function filtrarEntrada(input) {
    // Remove qualquer caractere que não seja um número
    input.value = input.value.replace(/[^0-9]/g, '');
}

// Função para fechar o modal de edição
function closeEditModal() {
    document.getElementById('modal-edit').style.display = 'none';
}

// Adiciona evento ao botão de cancelar para fechar o modal
document.querySelector('.cancelar').addEventListener('click', closeEditModal);

// Função para formatar o horário
function formatarHorario(input) {
    let valor = input.value;
    valor = valor.replace(/\D/g, ''); // Remove qualquer coisa que não seja número

    if (valor.length > 2) {
        valor = valor.slice(0, 2) + ':' + valor.slice(2); // Adiciona os dois pontos após o segundo dígito
    }

    // Se já tiver 5 caracteres, faz a validação
    if (valor.length >= 5) {
        let horas = parseInt(valor.slice(0, 2));
        let minutos = parseInt(valor.slice(3, 5));

        // Se as horas forem maiores que 23 ou os minutos forem maiores que 59, redefine para 00:00
        if (horas > 23 || minutos > 59) {
            valor = '00:00';
        } else {
            // Limita horas a 23
            if (horas > 23) {
                horas = 23;
            }

            // Limita minutos a 59
            if (minutos > 59) {
                minutos = 59;
            }

            // Formata o valor final
            valor = ('0' + horas).slice(-2) + ':' + ('0' + minutos).slice(-2);
        }
    }

    input.value = valor.slice(0, 5); // Limita o valor a 5 caracteres (HH:MM)
}

// Adiciona o evento de input para o campo de horário
document.getElementById('editHorarioChegada').addEventListener('input', function(e) {
    formatarHorario(e.target);
});

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
};

// Adiciona evento para atualização de linhas por página
document.getElementById('rows-per-page').addEventListener('change', updateRows);

// Evento de envio do formulário de edição
document.getElementById('editForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    const id = document.getElementById('editId').value;
    const kmChegada = document.getElementById('editKmChegada').value;
    const horarioChegada = document.getElementById('editHorarioChegada').value;

    // Se KM Chegada estiver vazio, defina um valor padrão (ex: 0)
    const kmValueToSend = kmChegada === '' ? '0' : kmChegada;

    // Cria os dados que serão enviados
    const formData = new FormData();
    formData.append('id', id);
    formData.append('km_chegada', kmValueToSend);
    formData.append('horario_chegada', horarioChegada);

    // Envia os dados via AJAX para o PHP
    fetch('../cadastros/config/tabela_veiculos_config.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // Exibe a resposta do PHP

        // Atualizar a linha na tabela
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            row.cells[6].textContent = kmValueToSend; // KM Chegada
            row.cells[8].textContent = horarioChegada; // Horário Chegada
        }

        // Fecha o modal e exibe a mensagem de sucesso
        closeEditModal();
        const launcher = document.getElementById('launcher');
        launcher.classList.remove('hidden');
        setTimeout(() => {
            launcher.classList.add('hidden');
        }, 2000);
    })
    .catch(error => {
        console.error('Erro ao salvar os dados:', error);
    });
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

// Função para atualizar a linha da tabela com os novos dados
function updateTableRow(id, km_chegada, horario_chegada) {
    if (!id) {
        console.error('ID não fornecido.');
        return;
    }
    
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        console.log(`Atualizando a linha com ID ${id}`); // Log para depuração
        row.querySelector('.km_chegada').textContent = km_chegada;
        row.querySelector('.horario_chegada').textContent = horario_chegada;
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}

// Função para filtrar a tabela
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




