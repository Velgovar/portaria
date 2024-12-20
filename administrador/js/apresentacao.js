let currentPage = 1;
let rowsPerPage = parseInt(localStorage.getItem('rowsPerPage')) || parseInt(document.getElementById('rows-per-page').value) || 10;
const table = document.getElementById('veiculos-list');
let tableRows = Array.from(table.querySelectorAll('tr')); 
let totalRows = tableRows.length;
let totalPages = Math.ceil(totalRows / rowsPerPage);

// Seleciona o modal, o botão de fechar e o botão de cancelar
const modal = document.getElementById("editModal");
const cancelButton = document.getElementById("cancelButton");

// Função para remover hífens e tratar valores vazios
function removerHifen(valor) {
    return (valor === '-' || valor.trim() === '') ? '' : valor;
}

// Função para abrir o modal e preencher os dados
function openEditModal(row) {
    const id = row.getAttribute('data-id'); // Pega o ID da linha
    const data = removerHifen(row.querySelector('td:nth-child(2)').textContent);
    const porteiro = removerHifen(row.querySelector('td:nth-child(3)').textContent);
    const horario_apresentacao = removerHifen(row.querySelector('td:nth-child(4)').textContent);
    const nome = removerHifen(row.querySelector('td:nth-child(5)').textContent);
    const empresa = removerHifen(row.querySelector('td:nth-child(6)').textContent);

    // Preencher o formulário no modal com os dados
    document.getElementById('data').value = data;
    document.getElementById('porteiro').value = porteiro;
    document.getElementById('horario_apresentacao').value = formatarHorario(horario_apresentacao);
    document.getElementById('nome').value = nome;
    document.getElementById('empresa').value = empresa;

    // Armazene o ID da linha no modal para ser usado depois
    $('#editForm').data('id', id);

    // Exibir o modal
    modal.style.display = "flex"; // Certifique-se de que é "flex" para centralizar
}

// Adiciona o evento ao botão de editar
document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function() {
        const row = button.closest('tr');
        openEditModal(row);
    });
});

// Função para fechar o modal
cancelButton.addEventListener('click', function() {
    modal.style.display = "none"; // Esconde o modal ao clicar em cancelar
});

// Função para formatar horário removendo os segundos
function formatarHorario(horario) {
    if (horario.includes(':')) {
        const partes = horario.split(':');
        return partes[0] + ':' + partes[1]; // Retorna horas e minutos
    }
    return horario;
}

$('#editForm').on('submit', function(e) {
    e.preventDefault(); // Impede o envio tradicional do formulário

    // Pega o valor da data do campo de input (no formato dd/mm/aaaa)
    var dataInput = $('#data').val();

// Converte a data no formato dd/mm/yyyy para yyyy-mm-dd, ou usa '0000-00-00' se estiver vazia
var dataConvertida = dataInput ? dataInput.split('/').reverse().join('-') : '0000-01-01';

// Coleta os dados do formulário, garantindo que campos vazios sejam preenchidos com valores padrão
var formData = {
    id: $('#editForm').data('id'), // Pega o ID da linha editada do modal
    data: dataConvertida, // Envia a data no formato aaaa-mm-dd ou '0000-00-00' se vazio
    porteiro: $('#porteiro').val() || '-', // Se estiver vazio, envia '-'
    horario_apresentacao: $('#horario_apresentacao').val() || '-', // Se estiver vazio, envia '-'
    nome: $('#nome').val() || '-', // Se estiver vazio, envia '-'
    empresa: $('#empresa').val() || '-', // Se estiver vazio, envia '0'
};

    // Requisição Ajax para enviar os dados para o arquivo config.php
    $.ajax({
        url: 'config/apresentacao_config.php', // URL do arquivo PHP que irá processar os dados
        type: 'POST', // Método de envio
        data: formData, // Dados a serem enviados
        success: function(response) {
            // O que fazer em caso de sucesso
            modal.style.display = "none"; // Fecha o modal
            $('#editForm')[0].reset(); // Limpa o formulário
            // Atualizar a tabela localmente com os novos dados
            updateTableRow(formData);

            // Exibe uma notificação de sucesso usando o launcher
            showLauncher("Registro atualizado com sucesso!");
        },
        error: function(xhr, status, error) {
            // O que fazer em caso de erro
            console.error(xhr.responseText); // Exibe o erro no console

            // Exibe uma notificação de erro usando o launcher
            showLauncher("Erro ao salvar o registro. Tente novamente.", true);
        }
    });
});

// Função para formatar automaticamente enquanto digita
$('#data').on('input', function() {
    var valor = $(this).val();

    // Remove qualquer caractere que não seja número ou barra
    valor = valor.replace(/[^0-9\/]/g, '');

    // Verifica se a barra deve ser adicionada
    if (valor.length >= 2 && valor[2] !== '/') {
        valor = valor.substring(0, 2) + '/' + valor.substring(2);
    }
    if (valor.length >= 5 && valor[5] !== '/') {
        valor = valor.substring(0, 5) + '/' + valor.substring(5);
    }

    // Limita o campo a 10 caracteres (dd/mm/aaaa)
    if (valor.length > 10) {
        valor = valor.substring(0, 10);
    }

    // Atualiza o campo com o valor formatado
    $(this).val(valor);
});

// Permite apagar a barra sem problemas e limita a 10 caracteres
$('#data').on('keydown', function(e) {
    var valor = $(this).val();
    
    // Verifica se a tecla pressionada é Backspace ou Delete
    if (e.key === 'Backspace' || e.key === 'Delete') {
        // Se a barra for apagada, reformatar os caracteres ao redor
        if (valor.length === 3 || valor.length === 6) {
            // Remove a barra apenas quando a tecla Backspace ou Delete for pressionada
            var novaValor = valor.substring(0, valor.length - 1); 
            $(this).val(novaValor);
        }
    }
});

// Previne a inserção de mais de 10 caracteres no campo
$('#data').on('paste', function(e) {
    var pastedData = e.originalEvent.clipboardData.getData('text');
    if (pastedData.length > 10) {
        e.preventDefault();
    }
});

// Função para formatar a data no formato dd/mm/aaaa
function formatarData(data) {
    var partes = data.split('-'); // Divide a data no formato aaaa-mm-dd
    return partes[2] + '/' + partes[1] + '/' + partes[0]; // Retorna no formato dd/mm/aaaa
}

// Função para atualizar a linha na tabela com os dados editados
function updateTableRow(data) {
    const row = document.querySelector(`tr[data-id="${data.id}"]`);
    row.querySelector('td:nth-child(2)').textContent = formatarData(data.data); // Formata a data antes de atualizar
    row.querySelector('td:nth-child(3)').textContent = data.porteiro;
    row.querySelector('td:nth-child(4)').textContent = data.horario_apresentacao;
    row.querySelector('td:nth-child(5)').textContent = data.nome;
    row.querySelector('td:nth-child(6)').textContent = data.empresa;
}

// Função para salvar os dados e atualizar a tabela
$('#salvar').on('click', function() {
    var formData = {
        id: $('#editForm').data('id'),
        data: $('#data').val(),
        porteiro: $('#porteiro').val(),
        horario_apresentacao: $('#horario_apresentacao').val(),
        nome: $('#nome').val(),
        empresa: $('#empresa').val(),
    };
    
    // Atualiza a tabela com os novos dados
    updateTableRow(formData);

    // Opcional: Pode adicionar um alerta ou mensagem de sucesso
    alert('Registro salvo com sucesso!');
});

// Função para filtrar a tabela
function filterTable() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const searchSelect = document.getElementById('search-select').value;  // Valor do select
    console.log("Filtro aplicado com valor de select:", searchSelect);  // Debug
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

    // Salva o valor selecionado no select no localStorage
    console.log("Salvando no localStorage:", searchSelect);  // Debug
    localStorage.setItem('searchSelectValue', searchSelect);
}

let recordIdToDelete = null; // Variável para armazenar o ID do registro a ser excluído

// Exibe o modal de confirmação de exclusão
function openConfirmationModal(recordId) {
    recordIdToDelete = recordId; // Armazena o ID do registro
    document.getElementById('confirmationModal').style.display = 'flex'; // Exibe o modal
}

// Fecha o modal de confirmação
function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none'; // Esconde o modal
    document.getElementById('confirmationInput').value = ''; // Limpa o campo de input
}

// Lida com a submissão do formulário de confirmação
document.getElementById('confirmationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio do formulário
    const confirmationInput = document.getElementById('confirmationInput').value.trim();

    // Verifica se o texto digitado é 'excluir'
    if (confirmationInput.toLowerCase() === 'excluir') {
        console.log("Iniciando a exclusão..."); // Log para verificar o processo de exclusão

        // Realiza a requisição de exclusão ao servidor
        const formData = new FormData();
        formData.append('id', recordIdToDelete);
        formData.append('confirmacao', 'excluir'); // Envia o valor de confirmação

        fetch('config/apresentacao_config.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erro na resposta do servidor");
            }
            return response.json(); // Espera um JSON como resposta
        })
        .then(result => {
            console.log("Resposta do servidor: ", result); // Log para depurar a resposta do servidor

            if (result.success) {
                // Exclui a linha imediatamente após a confirmação
                const rowToDelete = document.querySelector(`tr[data-id="${recordIdToDelete}"]`);
                if (rowToDelete) {
                    rowToDelete.style.display = 'none'; // Esconde a linha imediatamente
                }

                // Exibe uma notificação de sucesso usando o launcher
                showLauncher("Registro excluído com sucesso!");
                closeConfirmationModal(); // Fecha o modal após o sucesso

                // Exibe a notificação por 2 segundos, e recarrega a página depois
                setTimeout(() => {
                    location.reload(); // Recarrega a página após 2 segundos
                }, 2000);
            } else {
                // Exibe uma notificação de erro usando o launcher
                showLauncher("Erro ao excluir o registro: " + result.message, true);
            }
        })
        .catch(error => {
            console.error('Erro ao excluir o registro:', error);
            // Exibe uma notificação de erro usando o launcher
            showLauncher('Ocorreu um erro ao excluir o registro.', true);
        });
    } else {
        // Exibe uma notificação de erro usando o launcher
        showLauncher('Por favor, digite "excluir" para confirmar.', true);
    }
});

// Função para inicializar os botões de excluir
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const recordId = this.getAttribute('data-id');
            openConfirmationModal(recordId); // Abre o modal com o ID do registro
        });
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

// Função para formatar o horário (removendo os segundos)
function formatarHorario(horario) {
    // Remove os segundos se houver e mantém apenas o formato HH:MM
    if (horario && horario.includes(':')) {
        return horario.split(':').slice(0, 2).join(':'); // Divide o horário em partes e usa apenas horas e minutos
    }
    return horario; // Se não houver um valor válido, retorna o valor original
}

// Função para formatar o horário (no campo de input)
function formatarHorarioInput(input) {
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
document.getElementById('horario_apresentacao').addEventListener('input', function(e) {
    formatarHorarioInput(e.target);
});

function updateRows() {
    rowsPerPage = parseInt(document.getElementById('rows-per-page').value);
    localStorage.setItem('rowsPerPage', rowsPerPage); 
    totalRows = tableRows.length;
    totalPages = Math.ceil(totalRows / rowsPerPage);
    currentPage = 1; 
    renderTable();
}

function renderTable() {
    tableRows.forEach((row, index) => {
        row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? '' : 'none';
    });

    document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('next-page').classList.toggle('disabled', currentPage === totalPages);
    document.getElementById('first-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('last-page').classList.toggle('disabled', currentPage === totalPages);

    const pageLinks = document.getElementById('page-links');
    pageLinks.innerHTML = '';

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

window.onload = function() {
    const rowsPerPageSelector = document.getElementById('rows-per-page');
    rowsPerPageSelector.value = rowsPerPage;
    renderTable();
};

document.getElementById('rows-per-page').addEventListener('change', updateRows);

