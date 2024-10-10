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

            fetch('config/visitantes_config.php', {
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

// Função para validar os campos de entrada
function validateInput(input) {
    if (input.tagName === 'SELECT') {
        if (input.value === '') {
            input.classList.add('error');
            input.classList.remove('valid');
            setTimeout(function() {
                input.classList.remove('error');
            }, 3000);
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
        // Só validar se o campo estiver visível
        if (placaField.style.display !== 'none') {
            // Expressões regulares para os dois formatos de placa
            const mercosulRegex = /^[A-Z]{3}\d[A-Z]\d{2}$/; // ABC1D23
            const antigoRegex = /^[A-Z]{3}-\d{4}$/; // ABC-1234 (com hífen)
            
            // Verifica se o valor corresponde a um dos dois formatos
            if (!mercosulRegex.test(input.value.trim()) && !antigoRegex.test(input.value.trim())) {
                input.classList.add('error');
                input.classList.remove('valid');
                setTimeout(function() {
                    input.classList.remove('error');
                }, 3000);
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
    } else if (input.id === 'cpf') {
        if (!isValidCPF(input.value)) {
            input.classList.add('error');
            input.classList.remove('valid');
            setTimeout(function() {
                input.classList.remove('error');
            }, 3000);
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
            setTimeout(function() {
                input.classList.remove('error');
            }, 3000);
            return false;
        } else {
            input.classList.remove('error');
            input.classList.add('valid');
            return true;
        }
    }
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

// Variável para armazenar o valor anterior do CPF
let previousCPFValue = '';

// Variáveis para armazenar os valores antigos dos campos
let previousFormValues = {
    nome: '',
    tipovisitante: '',
    servico: '',
    empresa: ''
};

// Função para formatar o CPF
function formatCPF(value) {
    const cleanedValue = value.replace(/\D/g, '').slice(0, 11);
    const match = cleanedValue.match(/^(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})$/);
    if (match) {
        return `${match[1]}${match[2] ? '.' : ''}${match[2]}${match[3] ? '.' : ''}${match[3]}${match[4] ? '-' : ''}${match[4]}`;
    }
    return value;
}

// Função para verificar se o CPF é válido (precisa de 11 dígitos)
function isValidCPF(cpf) {
    const cleanedCPF = cpf.replace(/\D/g, '');
    return cleanedCPF.length === 11;
}

// Função para armazenar os valores atuais dos campos
function storeCurrentFormValues() {
    previousFormValues = {
        nome: document.getElementById('nome').value || '',
        tipovisitante: document.getElementById('tipovisitante').value || '',
        servico: document.getElementById('servico').value || '',
        empresa: document.getElementById('empresa').value || ''
    };
}

// Função para restaurar os valores antigos dos campos
function restorePreviousFormValues() {
    document.getElementById('nome').value = previousFormValues.nome;
    document.getElementById('tipovisitante').value = previousFormValues.tipovisitante;
    document.getElementById('servico').value = previousFormValues.servico;
    document.getElementById('empresa').value = previousFormValues.empresa;
}

// Manipulador de evento para o campo CPF
var cpfInput = document.getElementById('cpf');
cpfInput.addEventListener('input', function(event) {
    const value = event.target.value;
    const cleanedValue = value.replace(/\D/g, ''); // Remover caracteres não numéricos

    // Limitar o CPF a 11 dígitos
    if (cleanedValue.length > 11) {
        event.target.value = formatCPF(cleanedValue.slice(0, 11));
        return; // Ignorar o restante da execução se o limite for ultrapassado
    }

    event.target.value = formatCPF(value);

    // Se o CPF for válido (11 dígitos), buscar as informações
    if (isValidCPF(value)) {
        // Armazenar os valores atuais antes de buscar novos
        storeCurrentFormValues();
        fetchVisitorInfo(value);
    } 
    // Se o CPF está incompleto ou sendo apagado, restaurar os valores antigos
    else if (cleanedValue.length < previousCPFValue.replace(/\D/g, '').length) {
        // Apenas restaurar se o CPF anterior tinha 11 dígitos
        if (previousCPFValue.replace(/\D/g, '').length === 11) {
            restorePreviousFormValues();
        }
    }

    // Atualizar o valor anterior do CPF
    previousCPFValue = value;
});

// Função para buscar informações do visitante
function fetchVisitorInfo(cpf) {
    // Remove caracteres especiais do CPF para a consulta
    const cleanedCPF = cpf.replace(/\D/g, '');
    if (cleanedCPF.length === 11) {
        const url = 'config/cpf_config.php?cpf=' + encodeURIComponent(cpf);
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
                        restorePreviousFormValues();
                    }
                } catch (e) {
                    console.error('Erro ao processar a resposta:', e);
                    displayErrorMessage('Erro ao processar os dados do visitante.');
                    restorePreviousFormValues();
                }
            })
            .catch(function(error) {
                console.error('Erro:', error);
                displayErrorMessage('Erro ao buscar informações.');
                restorePreviousFormValues();
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
   
// Função para formatar a placa com base na presença do hífen
function formatPlaca(value) {
    // Remove tudo que não seja letra, número ou hífen
    const cleanedValue = value.replace(/[^A-Za-z0-9-]/g, '').toUpperCase();
    
    let formattedPlaca = '';
    
    if (cleanedValue.includes('-')) {
        // Remove hífen para processamento
        const parts = cleanedValue.split('-');
        const letras = parts[0].slice(0, 3).replace(/[^A-Z]/g, '');
        const numeros = parts[1].slice(0, 4).replace(/[^0-9]/g, '');
        formattedPlaca = `${letras}-${numeros}`;
    } else {
        // Processa sem hífen
        const letras = cleanedValue.slice(0, 3).replace(/[^A-Z]/g, '');
        const numero1 = cleanedValue.slice(3, 4).replace(/[^0-9]/g, '');
        const letra = cleanedValue.slice(4, 5).replace(/[^A-Z]/g, '');
        const numerosRestantes = cleanedValue.slice(5, 7).replace(/[^0-9]/g, '');
        formattedPlaca = `${letras}${numero1}${letra}${numerosRestantes}`;
    }
    
    // Garantir que o formato tenha exatamente 7 caracteres ou 8 com hífen
    return formattedPlaca.slice(0, 8);
}

// Manipulador de evento para o campo de placa
var placaInput = document.getElementById('placa');
placaInput.addEventListener('input', function(event) {
    const value = event.target.value;
    event.target.value = formatPlaca(value);
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
document.getElementById('horario_saida').addEventListener('input', function(e) {
    formatarHorario(e.target);
});

document.getElementById('horario_entrada').addEventListener('input', function(e) {
    formatarHorario(e.target);
});
