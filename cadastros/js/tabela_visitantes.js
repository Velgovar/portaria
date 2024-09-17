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

document.addEventListener('DOMContentLoaded', function() {
    const horarioSaida = document.getElementById('editHorarioSaida');

    function setupTimeScrollPlugin(flatpickrInstance) {
        function handleWheel(event) {
            event.preventDefault(); // Previne o comportamento padrão do scroll

            const currentDate = flatpickrInstance.selectedDates[0] || new Date();
            const increment = event.deltaY < 0 ? 1 : -1; // Determina a direção do scroll
            const minutes = currentDate.getMinutes() + increment;

            if (minutes >= 60) {
                currentDate.setHours(currentDate.getHours() + 1);
                currentDate.setMinutes(0);
            } else if (minutes < 0) {
                currentDate.setHours(currentDate.getHours() - 1);
                currentDate.setMinutes(59);
            } else {
                currentDate.setMinutes(minutes);
            }

            flatpickrInstance.setDate(currentDate, true); // Atualiza o valor no Flatpickr
        }

        flatpickrInstance._input.addEventListener('wheel', handleWheel);

        flatpickrInstance._input.addEventListener('mousedown', function(event) {
            event.stopPropagation(); // Previne que o clique seja interceptado por outros ouvintes
        });

        flatpickrInstance._input.addEventListener('focus', function(event) {
            flatpickrInstance._input.setSelectionRange(0, flatpickrInstance._input.value.length);
        });

        flatpickrInstance._input.addEventListener('click', function(event) {
            event.stopPropagation(); // Garante que o clique no campo seja tratado corretamente
        });

        flatpickrInstance._input.addEventListener('blur', function(event) {
            // Verifica se o campo está vazio e limpa a data selecionada
            if (flatpickrInstance._input.value.trim() === '') {
                flatpickrInstance.clear(); // Limpa a data selecionada se o campo estiver vazio
                flatpickrInstance.setDate('', false); // Define o valor do campo como vazio
            }
        });

        // Desabilita a confirmação com a tecla Enter
        flatpickrInstance._input.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) { // Verifica se a tecla pressionada é o Enter
                event.preventDefault(); // Previne o comportamento padrão de confirmar
            }
        });

        flatpickrInstance.config.onOpen.push(function() {
            if (flatpickrInstance._input.value.trim() === '') {
                flatpickrInstance.clear();
                flatpickrInstance.setDate('', false); // Define o valor do campo como vazio
            }
        });
    }

    // Aplicando o Flatpickr ao campo "Horário de Saída"
    flatpickr(horarioSaida, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        defaultHour: null, // Remove o preenchimento automático com valor padrão
        defaultMinute: null, // Remove o preenchimento automático com valor padrão
        onReady: function(selectedDates, dateStr, instance) {
            setupTimeScrollPlugin(instance);
        }
    });
});
