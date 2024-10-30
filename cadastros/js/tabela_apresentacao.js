let currentPage = 1;
let rowsPerPage = parseInt(localStorage.getItem('rowsPerPage')) || parseInt(document.getElementById('rows-per-page').value); 
const table = document.getElementById('veiculos-list');
let tableRows = Array.from(table.querySelectorAll('tr')); 
let totalRows = tableRows.length;
let totalPages = Math.ceil(totalRows / rowsPerPage);

function updateRows() {
    rowsPerPage = parseInt(document.getElementById('rows-per-page').value);
    localStorage.setItem('rowsPerPage', rowsPerPage); 
    totalRows = tableRows.length;
    totalPages = Math.ceil(totalRows / rowsPerPage);
    currentPage = 1; 
    renderTable();
}

function renderTable() {
    // Ocultar todas as linhas
    tableRows.forEach((row, index) => {
        row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? '' : 'none';
    });

    // Atualizar o estado dos botões de paginação
    document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('next-page').classList.toggle('disabled', currentPage === totalPages);
    document.getElementById('first-page').classList.toggle('disabled', currentPage === 1);
    document.getElementById('last-page').classList.toggle('disabled', currentPage === totalPages);

    // Atualizar links de página
    const pageLinks = document.getElementById('page-links');
    pageLinks.innerHTML = '';

    // Calcular o intervalo de páginas a ser exibido
    const numLinks = 3;
    let startPage = Math.max(1, currentPage - Math.floor(numLinks / 2));
    let endPage = Math.min(totalPages, startPage + numLinks - 1);

    if (endPage - startPage + 1 < numLinks) {
        startPage = Math.max(1, endPage - numLinks + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const link = document.createElement('a');
        link.textContent = i;
        link.href = '#';
        link.className = (i === currentPage) ? 'active' : '';
        link.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            renderTable();
        });
        pageLinks.appendChild(link);
    }
}

// Event listeners for pagination buttons
document.getElementById('prev-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
});

document.getElementById('next-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
});

document.getElementById('first-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage = 1;
        renderTable();
    }
});

document.getElementById('last-page').addEventListener('click', (e) => {
    e.preventDefault();
    if (currentPage < totalPages) {
        currentPage = totalPages;
        renderTable();
    }
});


// Inicializar a tabela e botões ao carregar a página
window.onload = function() {
    // Configura o valor inicial do seletor de linhas por página
    const rowsPerPageSelector = document.getElementById('rows-per-page');
    rowsPerPageSelector.value = rowsPerPage;
    renderTable();
};

// Adiciona evento para atualização de linhas por página
document.getElementById('rows-per-page').addEventListener('change', updateRows);

// Função para filtrar a tabela
function filterTable() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const searchSelect = document.getElementById('search-select').value;  // Valor do select
    console.log("Filtro aplicado com valor de select:", searchSelect);  // Debug
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

    // Salva o valor selecionado no select no localStorage
    console.log("Salvando no localStorage:", searchSelect);  // Debug
    localStorage.setItem('searchSelectValue', searchSelect);
}





