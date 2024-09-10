document.addEventListener('DOMContentLoaded', function() {
    setDefaultDateTime();

    var formIsProcessing = false;
    var submitButton = document.getElementById('submit-btn');

    document.getElementById('vehicle-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        if (formIsProcessing) {
            return; // Sai da função se estiver sendo processado
        }

        var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
        var formIsValid = true;

        inputs.forEach(function(input) {
            formIsValid = validateInput(input) && formIsValid;
        });

        if (formIsValid) {
            formIsProcessing = true;
            submitButton.disabled = true;

            var formData = new FormData(this);

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

    var inputs = document.querySelectorAll('#vehicle-form input, #vehicle-form select');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validateInput(input);
        });
    });

    function validateInput(input) {
        if (input.tagName === 'SELECT') {
            if (input.value === '') {
                input.classList.add('error');
                input.classList.remove('valid');
                removeErrorAfterDelay(input, 3000); // Diminuiu para 3 segundos
                return false;
            } else {
                input.classList.remove('error');
                input.classList.add('valid');
                return true;
            }
        } else if (input.id === 'km_chegada' || input.id === 'horario_entrada') {
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        } else {
            if (input.value.trim() === '') {
                input.classList.add('error');
                input.classList.remove('valid');
                removeErrorAfterDelay(input, 3000); // Diminuiu para 3 segundos
                return false;
            } else {
                input.classList.remove('error');
                input.classList.add('valid');
                return true;
            }
        }
    }

    function removeErrorAfterDelay(input, delay) {
        setTimeout(function() {
            input.classList.remove('error');
        }, delay);
    }

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

    function resetForm() {
        var form = document.getElementById('vehicle-form');
        form.reset();

        var inputs = form.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.classList.remove('valid', 'error');
        });

        setDefaultDateTime();
    }

    function setDefaultDateTime() {
        var today = new Date();
        var date = today.toISOString().substr(0, 10);
        var time = today.toTimeString().substr(0, 5);

        document.getElementById('date').value = date;
        document.getElementById('horario_saida').value = time;
    }

    function formatCPF(value) {
        const cleanedValue = value.replace(/\D/g, '').slice(0, 11);
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
            let cleanedValue = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 7);
            let formattedValue = '';
            if (cleanedValue.length >= 3) {
                formattedValue += cleanedValue.slice(0, 3);
            }
            if (cleanedValue.length >= 4) {
                formattedValue += cleanedValue.charAt(3);
            }
            if (cleanedValue.length >= 5) {
                formattedValue += cleanedValue.charAt(4);
            }
            if (cleanedValue.length > 5) {
                formattedValue += cleanedValue.slice(5);
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
