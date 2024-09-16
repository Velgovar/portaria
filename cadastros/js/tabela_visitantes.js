/// Função para abrir o modal e preencher o campo horario_saida com os dados mais recentes da linha
document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        
        // Seleciona a linha correspondente pelo ID
        const row = document.querySelector(`tr[data-id="${id}"]`);
        
        if (row) {
            // Pegue o valor mais recente do campo horario_saida da linha da tabela
            const horarioSaida = row.querySelector('.horario_saida').textContent.trim();

            // Preenche o campo do modal com o valor mais recente
            document.getElementById('editId').value = id;
            document.getElementById('editHorarioSaida').value = horarioSaida || '';

            // Abre o modal
            document.getElementById('editModal').style.display = 'flex';
        } else {
            console.error(`Linha com ID ${id} não encontrada.`);
        }
    });
});



// Fechar o modal ao clicar no botão de fechar
document.getElementById('closeModal').addEventListener('click', function() {
    closeModalFunction();
});

// Fechar o modal ao clicar fora dele
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('editModal')) {
        closeModalFunction();
    }
});

// Função para fechar o modal e resetar os campos
function closeModalFunction() {
    document.getElementById('editModal').style.display = 'none';
    resetModalFields();
}

// Função para resetar os campos do modal
function resetModalFields() {
    document.getElementById('editForm').reset();
}

// Fechar o modal ao clicar no botão "Cancelar"
document.getElementById('cancelButton').addEventListener('click', closeModalFunction);

// Enviar a atualização via AJAX
document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    const formData = new FormData(this);

    fetch('config/tabela_visitantes_config.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

        if (data.message.includes('sucesso')) {
            // Mostrar o launcher de notificação
            const launcher = document.getElementById('launcher');
            launcher.classList.remove('hidden');
            launcher.classList.add('visible');
            
            // Atualizar a linha da tabela
            updateTableRow(
                formData.get('id'),
                formData.get('horario_saida') // Atualiza o horário de saída
            );
            
            // Remover o launcher após 2 segundos
            setTimeout(() => {
                launcher.classList.remove('visible');
                launcher.classList.add('hidden');
            }, 2000);

            // Fechar o modal
            closeModalFunction();
        } else {
            console.error('Falha ao editar o registro:', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao enviar a atualização.');
    });
});



// Função para atualizar a linha da tabela com os novos dados
function updateTableRow(id, horarioSaida) {
    if (!id) {
        console.error('ID não fornecido.');
        return;
    }

    // Selecionar a linha da tabela com o ID fornecido
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        console.log(`Atualizando a linha com ID ${id}`); // Log para depuração

        // Formatar o horário de saída para remover os segundos
        const formattedHorarioSaida = formatTime(horarioSaida);

        // Atualizar o conteúdo da célula de horario_saida
        const horarioSaidaCell = row.querySelector('.horario_saida');
        if (horarioSaidaCell) {
            horarioSaidaCell.textContent = formattedHorarioSaida;
        } else {
            console.error('Célula horario_saida não encontrada.');
        }
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}

// Função para formatar o horário no formato HH:mm
function formatTime(timeString) {
    if (!timeString) return '';

    // Cria um objeto Date com a string de tempo
    const time = new Date(`1970-01-01T${timeString}Z`);
    
    // Formata o horário para HH:mm
    return time.toISOString().substring(11, 16);
}


// Função para alterar o número de registros por página
function changeRecordsPerPage() {
    const select = document.getElementById('registrosPorPagina');
    const registrosPorPagina = select.value;
    window.location.href = `?pagina=1&registrosPorPagina=${registrosPorPagina}`;
}

// Adicionar event listener para mudança no número de registros por página
document.getElementById('registrosPorPagina').addEventListener('change', changeRecordsPerPage);