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
                showLauncher(data.message); // Exibe mensagem de sucesso
                resetForm();
            })
            .catch(function(error) {
                showLauncher('Erro ao processar o formulário.', true); // Exibe mensagem de erro
            })
            .finally(function() {
                setTimeout(function() {
                    formIsProcessing = false;
                    submitButton.disabled = false;
                }, 1500);
            });
        } else {
            showLauncher('Por favor, preencha todos os campos corretamente.', true); // Exibe mensagem de erro
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
        } else if (input.id === 'km_chegada' || input.id === 'horario_saida') {
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

    // Função para validar todo o formulário
    function validateForm() {
        var isValid = true;
        inputs.forEach(function(input) {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Função para exibir mensagens de erro
    function showLauncher(message, isError = false) {
        var launcher = document.getElementById('launcher');
        var messageElement = launcher.querySelector('.launcher-message');

        messageElement.textContent = message;
        launcher.classList.remove('launcher-error');
        if (isError) {
            launcher.classList.add('launcher-error');
        }

        launcher.classList.remove('hidden');
        launcher.classList.add('launcher-show');

        setTimeout(function() {
            launcher.classList.remove('launcher-show');
            launcher.classList.add('hidden');
        }, 3000); // A mensagem desaparece após 3 segundos
    }

    // Função para redefinir o formulário
    function resetForm() {
        form.reset();

        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.classList.remove('valid', 'error');
        });
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

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    let isCalendarOpen = false; // Variável de controle para o estado do calendário
    let preventImmediateClose = false; // Variável para prevenir fechamento imediato

    const flatpickrInstance = flatpickr(dateInput, {
        locale: "pt", // Define o idioma para português
        dateFormat: "Y/m/d", // Formato de data como dia/mês/ano
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

document.addEventListener('DOMContentLoaded', function() {
    const horarioEntrada = document.getElementById('horario_entrada');
    const horarioSaida = document.getElementById('horario_saida');

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

        // Adiciona o ouvinte de evento de rotação do mouse ao campo de entrada
        flatpickrInstance._input.addEventListener('wheel', handleWheel);
    }

    flatpickr(horarioEntrada, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        onReady: function(selectedDates, dateStr, instance) {
            setupTimeScrollPlugin(instance);
        }
    });

    flatpickr(horarioSaida, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        onReady: function(selectedDates, dateStr, instance) {
            setupTimeScrollPlugin(instance);
        }
    });
});