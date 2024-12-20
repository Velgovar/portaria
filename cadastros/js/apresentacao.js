document.addEventListener('DOMContentLoaded', function() {
    setDefaultDateTime();

    var formIsProcessing = false;
    var submitButton = document.getElementById('submit-btn');

    document.getElementById('vehicle-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        if (formIsProcessing) {
            return; // Sai da função se o formulário já estiver sendo processado
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

            fetch('../cadastros/config/apresentacao_config.php', {
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
                removeErrorAfterDelay(input, 3000); // Remove o erro após 3 segundos
                return false;
            } else {
                input.classList.remove('error');
                input.classList.add('valid');
                return true;
            }
        } else {
            if (input.value.trim() === '') {
                input.classList.add('error');
                input.classList.remove('valid');
                removeErrorAfterDelay(input, 3000); // Remove o erro após 3 segundos
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
        document.getElementById('horario_apresentacao').value = time;
    }
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
document.getElementById('horario_apresentacao').addEventListener('input', function(e) {
    formatarHorario(e.target);
});


