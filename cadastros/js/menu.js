function toggleAdminLauncher() {
    const adminLauncher = document.getElementById('adminLauncher');
    if (adminLauncher.style.display === 'block') {
        adminLauncher.style.display = 'none';
    } else {
        adminLauncher.style.display = 'block';
    }
}

function openAdmin() {
    window.location.href = './administrador/login.php';
}

function openRamaisModal() {
    const modal = document.getElementById("myModal");
    const iframe = document.getElementById("modalIframe");
    iframe.src = "http://172.16.0.234/ramal/ramal.php"; // Substitua com o URL do conteúdo desejado
    modal.style.display = "block";
}

function openAjuda() {
    window.location.href = 'http://172.16.0.225/front/ticket.form.php';
}

function handleCadastrarRamais() {
    toggleAdminLauncher(); // Mostra o painel de administração
    openRamaisModal(); // Abre o modal de ramais
}

// Fechar o modal ao clicar no "x"
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('myModal').style.display = 'none';
});

// Fechar o modal ao clicar fora do conteúdo
window.addEventListener('click', function(event) {
    const modal = document.getElementById('myModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Fechar o painel de administração ao clicar fora dele
window.addEventListener('click', function(event) {
    const adminLauncher = document.getElementById('adminLauncher');
    const settingsIcon = document.querySelector('.settings-icon');
    
    // Verifica se o clique foi fora do painel de administração e do ícone de configurações
    if (event.target !== adminLauncher && event.target !== settingsIcon && !adminLauncher.contains(event.target)) {
        adminLauncher.style.display = 'none';
    }
});