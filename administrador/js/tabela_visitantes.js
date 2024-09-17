// Abrir o modal de edição
document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const data = this.getAttribute('data-data');
        const porteiro = this.getAttribute('data-porteiro');
        const nome = this.getAttribute('data-nome');
        const cpf = this.getAttribute('data-cpf');
        const tipoVisitante = this.getAttribute('data-tipovisitante');
        const servico = this.getAttribute('data-servico');
        const empresa = this.getAttribute('data-empresa');
        const estacionamento = this.getAttribute('data-estacionamento');
        const placa = this.getAttribute('data-placa');
        const horarioEntrada = this.getAttribute('data-horario_entrada');
        const horarioSaida = this.getAttribute('data-horario_saida');
        const colaborador = this.getAttribute('data-colaborador');
        const setor = this.getAttribute('data-setor');

        // Preencher os campos do modal com os dados do registro
        document.getElementById('editId').value = id;
        document.getElementById('editData').value = data || '';
        document.getElementById('editPorteiro').value = porteiro || '';
        document.getElementById('editNome').value = nome || '';
        document.getElementById('editCpf').value = cpf || '';
        document.getElementById('editTipoVisitante').value = tipoVisitante || '';
        document.getElementById('editServico').value = servico || '';
        document.getElementById('editEmpresa').value = empresa || '';
        document.getElementById('editEstacionamento').value = estacionamento || '';
        document.getElementById('editPlaca').value = placa || '';
        document.getElementById('editHorarioEntrada').value = horarioEntrada || '';
        document.getElementById('editHorarioSaida').value = horarioSaida || '';
        document.getElementById('editColaborador').value = colaborador || '';
        document.getElementById('editSetor').value = setor || '';

        // Abrir o modal
        document.getElementById('editModal').style.display = 'flex';
    });
});

// Fechar o modal ao clicar no botão "Cancelar"
document.getElementById('cancelButton').addEventListener('click', function() {
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

// Enviar a atualização via AJAX
// Lidar com o envio do formulário de edição
document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    const formData = new FormData(this);

    fetch('config/editar_teste.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta do servidor:', data); // Verifique a resposta

        // Ocultar a notificação padrão (em alguns navegadores, isso não pode ser feito diretamente, mas vamos garantir que só a nossa notificação seja exibida)
        const launcher = document.getElementById('launcher');
        if (data.message.includes('sucesso')) {
            // Mostrar a notificação personalizada
            launcher.classList.remove('hidden');
            launcher.classList.add('visible');

            // Remover a notificação personalizada após 2 segundos
            setTimeout(() => {
                launcher.classList.remove('visible');
                launcher.classList.add('hidden');
            }, 2000);

            // Fechar o modal e atualizar a linha da tabela
            closeModalFunction();
            updateTableRow(
                formData.get('id'),
                formData.get('data'),
                formData.get('porteiro'),
                formData.get('nome'),
                formData.get('cpf'),
                formData.get('tipovisitante'),
                formData.get('servico'),
                formData.get('empresa'),
                formData.get('estacionamento'),
                formData.get('placa'),
                formData.get('horario_entrada'),
                formData.get('horario_saida'),
                formData.get('colaborador'),
                formData.get('setor')
            );
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
function updateTableRow(id, data, porteiro, nome, cpf, tipovisitante, servico, empresa, estacionamento, placa, horarioEntrada, horarioSaida, colaborador, setor) {
    if (!id) {
        console.error('ID não fornecido.');
        return;
    }

    // Selecionar a linha da tabela com o ID fornecido
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        console.log(`Atualizando a linha com ID ${id}`); // Log para depuração

        // Atualizar o conteúdo das células
        const dataCell = row.querySelector('.data');
        const porteiroCell = row.querySelector('.porteiro');
        const nomeCell = row.querySelector('.nome');
        const cpfCell = row.querySelector('.cpf');
        const tipovisitanteCell = row.querySelector('.tipovisitante');
        const servicoCell = row.querySelector('.servico');
        const empresaCell = row.querySelector('.empresa');
        const estacionamentoCell = row.querySelector('.estacionamento');
        const placaCell = row.querySelector('.placa');
        const horarioEntradaCell = row.querySelector('.horario_entrada');
        const horarioSaidaCell = row.querySelector('.horario_saida');
        const colaboradorCell = row.querySelector('.colaborador');
        const setorCell = row.querySelector('.setor');

        if (dataCell) dataCell.textContent = data;
        if (porteiroCell) porteiroCell.textContent = porteiro;
        if (nomeCell) nomeCell.textContent = nome;
        if (cpfCell) cpfCell.textContent = cpf;
        if (tipovisitanteCell) tipovisitanteCell.textContent = tipovisitante;
        if (servicoCell) servicoCell.textContent = servico;
        if (empresaCell) empresaCell.textContent = empresa;
        if (estacionamentoCell) estacionamentoCell.textContent = estacionamento;
        if (placaCell) placaCell.textContent = placa;
        if (horarioEntradaCell) horarioEntradaCell.textContent = horarioEntrada;
        if (horarioSaidaCell) horarioSaidaCell.textContent = horarioSaida;
        if (colaboradorCell) colaboradorCell.textContent = colaborador;
        if (setorCell) setorCell.textContent = setor;

        console.log('Linha atualizada com sucesso.');
    } else {
        console.error(`Linha com ID ${id} não encontrada.`);
    }
}

// Variável global para armazenar o ID do item a ser excluído
let deleteId = null;

// Abrir o modal de confirmação de exclusão
document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', function() {
        deleteId = this.getAttribute('data-id'); // Define o ID do item a ser excluído
        document.getElementById('confirmationModal').style.display = 'flex'; // Abre o modal
    });
});

// Função para fechar o modal de confirmação
function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('confirmationInput').value = ''; // Limpa o campo de texto
}

// Função para confirmar a exclusão
document.getElementById('confirmationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    const confirmationInput = document.getElementById('confirmationInput').value.trim().toLowerCase();
    if (confirmationInput === 'excluir') {
        fetch('config/excluir_teste.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': deleteId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resposta do servidor:', data);

            if (data.status === 'success') {
                // Remove a linha da tabela
                const row = document.querySelector(`tr[data-id="${deleteId}"]`);
                if (row) {
                    row.remove();
                }

                // Mostra a mensagem de sucesso
                showMessage('Registro excluído com sucesso!', 'success');
            } else {
                // Mostra a mensagem de erro
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao excluir o registro.', 'error');
        });

        closeConfirmationModal(); // Fecha o modal após a confirmação
    } else {
        showMessage('Você deve digitar "excluir" para confirmar.', 'warning');
    }
});

// Função para mostrar mensagens
function showMessage(message, type) {
    const messageContainer = document.querySelector('.message-container');
    messageContainer.textContent = message;
    messageContainer.className = `message-container ${type}`; // Adiciona a classe de tipo (success, error, warning)
    setTimeout(() => {
        messageContainer.textContent = '';
    }, 2000);
}

// Fechar o modal de confirmação ao clicar fora dele
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('confirmationModal')) {
        closeConfirmationModal();
    }
});


// Função para alterar o número de registros por página
function changeRecordsPerPage() {
    const select = document.getElementById('registrosPorPagina');
    const registrosPorPagina = select.value;
    window.location.href = `?pagina=1&registrosPorPagina=${registrosPorPagina}`;
}

// Adicionar event listener para mudança no número de registros por página
document.getElementById('registrosPorPagina').addEventListener('change', changeRecordsPerPage);

