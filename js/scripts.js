document.addEventListener('DOMContentLoaded', function() {
    setDefaultDateTime();

    // Variável para controlar se o formulário está sendo processado
    var formIsProcessing = false;
    var submitButton = document.getElementById('submit-btn');

    // Adiciona ouvinte de evento para o envio do formulário
    document.getElementById('vehicle-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        // Verifica se o formulário está sendo processado
        if (formIsProcessing) {
            return; // Sai da função se estiver sendo processado
        }

        var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
        var formIsValid = true;

        inputs.forEach(function(input) {
            formIsValid = validateInput(input) && formIsValid;
        });

        if (formIsValid) {
            // Define o formulário como sendo processado
            formIsProcessing = true;

            // Desabilita o botão de submit para evitar cliques repetidos
            submitButton.disabled = true;

            // Captura dos dados do formulário
            var formData = new FormData(this);

            // Requisição para enviar os dados para o PHP
            fetch('../config/config.php', {
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
                // Exibe mensagem de sucesso
                displaySuccessMessage(data.message);
                resetForm();
            })
            .catch(function(error) {
                // Exibe mensagem de erro
                displayErrorMessage('Erro ao processar o formulário.');
            })
            .finally(function() {
                // Define novamente o formulário como não processado após 1.5 segundos
                setTimeout(function() {
                    formIsProcessing = false;
                    submitButton.disabled = false; // Reabilita o botão após o intervalo
                }, 1500);
            });
        } else {
            displayErrorMessage('Por favor, preencha todos os campos corretamente.');
        }
    });

    // Adiciona ouvintes de evento para validação em tempo real
    var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validateInput(input);
        });
    });

    // Função para validar um campo específico
    function validateInput(input) {
        if (input.tagName === 'SELECT') {
            // Se o campo for um select
            if (input.value === '') {
                input.classList.add('error');
                input.classList.remove('valid');
                return false;
            } else {
                input.classList.remove('error');
                input.classList.add('valid');
                return true;
            }
        } else if (input.id === 'km_chegada' || input.id === 'horario_chegada') {
            // Se o campo for KM chegada ou Horário chegada
            // Não faz validação obrigatória, apenas atualiza classes
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        } else {
            // Se o campo for um input padrão
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

    // Função para exibir mensagem de erro
    function displayErrorMessage(message) {
        var errorMessage = document.getElementById('error-message');
        errorMessage.textContent = message;
        errorMessage.classList.add('show');

        // Esconde a mensagem de erro após 1.5 segundos
        setTimeout(function() {
            errorMessage.classList.remove('show');
        }, 1500);
    }

    // Função para exibir mensagem de sucesso
    function displaySuccessMessage(message) {
        var successMessage = document.getElementById('success-message');
        successMessage.textContent = message;
        successMessage.classList.add('show');

        // Esconde a mensagem de sucesso após 1.5 segundos
        setTimeout(function() {
            successMessage.classList.remove('show');
        }, 1500);
    }

    // Função para limpar o formulário após envio bem-sucedido
    function resetForm() {
        var form = document.getElementById('vehicle-form');
        form.reset();

        // Resetar também as classes de validação
        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.classList.remove('valid', 'error');
        });

        // Reconfigura a data e horário de saída
        setDefaultDateTime();
    }

    // Função para definir a data e hora atuais nos campos apropriados
    function setDefaultDateTime() {
        var today = new Date();
        var date = today.toISOString().substr(0, 10);
        var time = today.toTimeString().substr(0, 5);

        document.getElementById('date').value = date;
        document.getElementById('horario_saida').value = time;
    }

    function formatCPF(value) {
        const cleanedValue = value.replace(/\D/g, '').slice(0, 11); // Limita a 11 números
        const match = cleanedValue.match(/^(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})$/);
        if (match) {
            return `${match[1]}${match[2] ? '.' : ''}${match[2]}${match[3] ? '.' : ''}${match[3]}${match[4] ? '-' : ''}${match[4]}`;
        }
        return value;
    }

    var cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(event) {
        const value = event.target.value;
        event.target.value = formatCPF(value);
    });

    document.addEventListener('DOMContentLoaded', function() {
        var placaInput = document.getElementById('placa');
    
        function formatPlaca(value) {
            // Remove caracteres não permitidos e converte para maiúsculo
            let cleanedValue = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 7);
            let formattedValue = '';
            if (cleanedValue.length >= 3) {
                formattedValue += cleanedValue.slice(0, 3); // Letras iniciais
            }
            if (cleanedValue.length >= 4) {
                formattedValue += cleanedValue.charAt(3); // Número no meio
            }
            if (cleanedValue.length >= 5) {
                formattedValue += cleanedValue.charAt(4); // Letra depois do número
            }
            if (cleanedValue.length > 5) {
                formattedValue += cleanedValue.slice(5); // Últimos números
            }
            return formattedValue;
        }
    
        placaInput.addEventListener('input', function(event) {
            const value = event.target.value;
            event.target.value = formatPlaca(value);
        });
    
        placaInput.addEventListener('blur', function(event) {
            const value = event.target.value;
            if (!/^[A-Z]{3}\d[A-Z]\d{3}$/.test(value)) {
                alert('Formato de placa inválido. Use o formato AAA1A11.');
                event.target.value = '';
            }
        });
    });
    
});
