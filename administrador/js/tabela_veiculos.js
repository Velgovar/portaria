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
        fetch('config/tabela_veiculos_config.php', {
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

    fetch('config/tabela_veiculos_config.php', {
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

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('editData'); // Mudando para o campo de data do modal
    let isCalendarOpen = false; // Variável de controle para o estado do calendário
    let preventImmediateClose = false; // Variável para prevenir fechamento imediato

    const flatpickrInstance = flatpickr(dateInput, {
        locale: "pt", // Define o idioma para português
        dateFormat: "Y/m/d", // Formato de data
        showMonths: 1, // Mostra apenas um mês por vez
        disableMonthNav: true, // Desabilita a navegação entre meses
        defaultDate: "today", // Define a data padrão como hoje
        onReady: function(selectedDates, dateStr, instance) {
            instance.calendarContainer.classList.add('only-current-month');
        },
        onOpen: function() {
            isCalendarOpen = true; // Atualiza o estado quando o calendário abre
            preventImmediateClose = true; // Impede fechamento imediato ao abrir
            setTimeout(() => preventImmediateClose = false, 200); // Libera após 200ms
        },
        onClose: function() {
            isCalendarOpen = false; // Atualiza o estado quando o calendário fecha
        }
    });

    // Função para alternar a exibição do calendário
    dateInput.addEventListener('click', function(event) {
        if (preventImmediateClose) return; // Previne o fechamento imediato

        if (isCalendarOpen) {
            flatpickrInstance.close(); // Fecha o calendário se estiver aberto
        } else {
            flatpickrInstance.open(); // Abre o calendário se estiver fechado
        }
    });
});

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

// Adiciona o evento de input para os dois campos
document.getElementById('editHorarioChegada').addEventListener('input', function(e) {
    formatarHorario(e.target);
});

// Adiciona o evento de input para os dois campos
document.getElementById('editHorarioSaida').addEventListener('input', function(e) {
    formatarHorario(e.target);
});
