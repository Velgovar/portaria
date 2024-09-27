// Função para abrir o modal e preencher os campos com os dados mais recentes da linha
function openModal(id) {
    // Seleciona a linha correspondente pelo ID
    const row = document.querySelector(`tr[data-id="${id}"]`);
    
    if (row) {
        // Pegue os valores mais recentes da linha da tabela
        const km_chegada = row.querySelector('.km_chegada').textContent.trim();
        const horario_chegada = row.querySelector('.horario_chegada').textContent.trim();

        // Preenche os campos do modal com os valores mais recentes
        document.getElementById('editId').value = id;
        document.getElementById('editKmChegada').value = km_chegada;
        document.getElementById('editHorarioChegada').value = horario_chegada;

        // Abra o modal
        document.getElementById('editModal').style.display = 'flex';
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}

// Função para fechar o modal
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Função para alterar o número de registros por página
function changeRecordsPerPage() {
    const select = document.getElementById('registrosPorPagina');
    const registrosPorPagina = select.value;
    window.location.href = `?pagina=1&registrosPorPagina=${registrosPorPagina}`;
}

// Função para manipular o envio do formulário via AJAX
function handleFormSubmit(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

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

            // Mostrar o launcher de notificação
            const launcher = document.getElementById('launcher');
            launcher.classList.remove('hidden');
            launcher.classList.add('visible');

            // Remover o launcher após 2 segundos
            setTimeout(() => {
                launcher.classList.remove('visible');
                launcher.classList.add('hidden');
            }, 2000);

            // Atualizar a linha da tabela com os novos dados
            updateTableRow(
                formData.get('id'),
                formData.get('km_chegada'),
                formData.get('horario_chegada')
            );
        } else {
            console.error('Falha ao editar o registro:', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    });
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

// Adicionar event listener ao formulário de edição
document.getElementById('editForm').addEventListener('submit', handleFormSubmit);

// Adicionar event listener para mudança no número de registros por página
document.getElementById('registrosPorPagina').addEventListener('change', changeRecordsPerPage);

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
