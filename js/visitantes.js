document.addEventListener('DOMContentLoaded', function() {
    // Configura a data e hora padrão ao carregar a página
    setDefaultDateTime();

    var formIsProcessing = false; // Flag para verificar se o formulário está sendo processado
    var submitButton = document.getElementById('submit-btn');
    var placaField = document.getElementById('placa').parentElement;
    placaField.style.display = 'none'; // Oculta o campo de placa inicialmente

    // Manipulador de evento para o envio do formulário
    document.getElementById('vehicle-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Evita o envio padrão do formulário

        if (formIsProcessing) {
            return; // Impede o envio se o formulário já estiver sendo processado
        }

        var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
        var formIsValid = true;

        // Valida todos os campos do formulário
        inputs.forEach(function(input) {
            formIsValid = validateInput(input) && formIsValid;
        });

        if (formIsValid) {
            formIsProcessing = true;
            submitButton.disabled = true;

            var formData = new FormData(this);

            // Envia os dados do formulário para o servidor
            fetch('../config/configr.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Erro ao enviar o formulário.');
                }
                return response.json();
            })
            .then(function(data) {
                displaySuccessMessage(data.message);
                resetForm();
            })
            .catch(function(error) {
                displayErrorMessage('Erro ao processar o formulário.');
            })
            .finally(function() {
                setTimeout(function() {
                    formIsProcessing = false;
                    submitButton.disabled = false;
                }, 2000);
            });
        } else {
            displayErrorMessage('Por favor, preencha todos os campos corretamente.');
        }
    });

    // Adiciona validadores aos campos do formulário
    var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validateInput(input);
        });
    });

    // Função para validar os campos de entrada
function validateInput(input) {
    if (input.tagName === 'SELECT') {
        if (input.value === '') {
            input.classList.add('error');
            input.classList.remove('valid');
            return false;
        } else {
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        }
    } else if (input.id === 'km_chegada') {
        input.classList.remove('error');
        input.classList.add('valid');
        return true;
    } else if (input.id === 'horario_saida') {
        // Campo horario_saida não é obrigatório
        input.classList.remove('error');
        input.classList.add('valid');
        return true;
    } else if (input.id === 'placa') {
        if (placaField.style.display !== 'none') {
            if (input.value.trim() === '') {
                input.classList.add('error');
                input.classList.remove('valid');
                return false;
            } else {
                input.classList.remove('error');
                input.classList.add('valid');
                return true;
            }
        } else {
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        }
    } else {
        if (input.value.trim() === '') {
            input.classList.add('error');
            input.classList.remove('valid');
            return false;
        } else {
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        }
    }
}


    // Função para exibir mensagens de erro
    function displayErrorMessage(message) {
        var errorMessage = document.getElementById('error-message');
        errorMessage.textContent = message;
        errorMessage.classList.add('show');

        setTimeout(function() {
            errorMessage.classList.remove('show');
        }, 1500);
    }

    // Função para exibir mensagens de sucesso
    function displaySuccessMessage(message) {
        var successMessage = document.getElementById('success-message');
        successMessage.textContent = message;
        successMessage.classList.add('show');

        setTimeout(function() {
            successMessage.classList.remove('show');
        }, 1500);
    }

    // Função para resetar o formulário
    function resetForm() {
        var form = document.getElementById('vehicle-form');
        form.reset();

        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.classList.remove('valid', 'error');
        });

        setDefaultDateTime();
        estacionamentoSelect.value = '';
        placaField.style.display = 'none';
    }

    // Função para definir a data e hora padrão
    function setDefaultDateTime() {
        var today = new Date();
        var date = today.toISOString().substr(0, 10);
        var time = today.toTimeString().substr(0, 5);

        document.getElementById('date').value = date;
        document.getElementById('horario_entrada').value = time;
    }

    // Função para formatar o CPF
    function formatCPF(value) {
        const cleanedValue = value.replace(/\D/g, '').slice(0, 11);
        const match = cleanedValue.match(/^(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})$/);
        if (match) {
            return `${match[1]}${match[2] ? '.' : ''}${match[2]}${match[3] ? '.' : ''}${match[3]}${match[4] ? '-' : ''}${match[4]}`;
        }
        return value;
    }

    // Função para verificar se o CPF é válido
    function isValidCPF(cpf) {
        const cleanedCPF = cpf.replace(/\D/g, '');
        return cleanedCPF.length === 11;
    }

    // Função para limpar os campos do formulário
    function clearFormFields() {
        document.getElementById('nome').value = '';
        document.getElementById('tipovisitante').value = '';
        document.getElementById('servico').value = '';
        document.getElementById('empresa').value = '';
    }

    // Manipulador de evento para o campo CPF
    var cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(event) {
        const value = event.target.value;
        event.target.value = formatCPF(value);

        if (isValidCPF(value)) {
            fetchVisitorInfo(value);
        } else {
            clearFormFields(); // Limpa os campos se o CPF não for válido
        }
    });

    // Função para buscar informações do visitante
    function fetchVisitorInfo(cpf) {
        // Remove caracteres especiais do CPF para a consulta
        const cleanedCPF = cpf.replace(/\D/g, '');
        if (cleanedCPF.length === 11) {
            const url = '../config/get_visitor_info.php?cpf=' + encodeURIComponent(cpf);
            console.log('Buscando informações com URL:', url); // Verifique no console
            fetch(url)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Erro ao buscar informações.');
                    }
                    return response.text(); // Recebe a resposta como texto
                })
                .then(function(text) {
                    try {
                        const data = JSON.parse(text); // Tenta converter o texto para JSON
                        console.log('Dados recebidos:', data); // Verifique no console
                        if (data.nome) { // Ajuste a verificação conforme a resposta esperada
                            document.getElementById('nome').value = data.nome;
                            document.getElementById('tipovisitante').value = data.tipovisitante;
                            document.getElementById('servico').value = data.servico;
                            document.getElementById('empresa').value = data.empresa;
                        } else {
                            clearFormFields();
                        }
                    } catch (e) {
                        console.error('Erro ao processar a resposta:', e);
                        displayErrorMessage('Erro ao processar os dados do visitante.');
                    }
                })
                .catch(function(error) {
                    console.error('Erro:', error);
                    displayErrorMessage('Erro ao buscar informações.');
                });
        }
    }
    
    // Manipulador de evento para o campo de estacionamento
    var estacionamentoSelect = document.getElementById('estacionamento');
    estacionamentoSelect.addEventListener('change', function() {
        if (estacionamentoSelect.value === 'Sim') {
            placaField.style.display = 'block';
        } else {
            placaField.style.display = 'none';
            document.getElementById('placa').value = '';
        }
    });
});
