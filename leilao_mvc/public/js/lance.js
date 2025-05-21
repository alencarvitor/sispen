// Funções para atualização em tempo real na página de lance
function atualizarDashboardLance(forceUpdate = false) {
    // Verificar se o produto ainda está em leilão
    const produtoId = document.getElementById('produto-atual-id')?.value;
    if (produtoId) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=verificar_produto_ativo&produto_id=${produtoId}`
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.ativo) {
                // Se o produto não estiver mais em leilão, recarregar a página
                window.location.reload();
                return;
            }
            
            // Atualizar maior lance e arrematante
            if (document.getElementById('status-leilao')?.value === 'em_andamento' || forceUpdate) {
                fetch('atualizar_leilao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=buscar_maior_lance&produto_id=${produtoId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('maior-arrematante').innerText = data.arrematante || 'Nenhum lance';
                        document.getElementById('maior-lance').innerText = data.lance ? `R$ ${data.lance}` : 'R$ 0,00';
                    }
                })
                .catch(err => console.error('Erro ao atualizar maior lance:', err));
            }
            
            // Atualizar tabelas de lances do usuário
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Atualizar tabela de lances do usuário
                    if (document.querySelector('.user-bids table')) {
                        document.querySelector('.user-bids table').innerHTML = doc.querySelector('.user-bids table').innerHTML;
                    }
                    
                    // Atualizar tabela de itens arrematados
                    if (document.querySelector('.user-won-items table')) {
                        document.querySelector('.user-won-items table').innerHTML = doc.querySelector('.user-won-items table').innerHTML;
                    }
                })
                .catch(err => console.error('Erro ao atualizar tabelas:', err));
        })
        .catch(err => console.error('Erro ao verificar produto:', err));
    }
    
    // Notificar outros usuários sobre a atualização
    if (forceUpdate) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=notificar_atualizacao'
        })
        .catch(err => console.error('Erro ao notificar atualização:', err));
    }
}

// Atualizar dashboard a cada 5 segundos
setInterval(atualizarDashboardLance, 5000);

// Função para dar lance sem reload
function darLance(event) {
    event.preventDefault();
    const valor = parseFloat(document.getElementById('valor-lance').value);
    if (isNaN(valor) || valor <= 0) {
        alert('Por favor, insira um valor válido.');
        return;
    }
    
    const produtoId = document.getElementById('produto-atual-id').value;
    
    // Verificar se o produto está em leilão ativo antes de tentar dar lance
    fetch('atualizar_leilao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=verificar_produto_ativo&produto_id=${produtoId}`
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success || !data.ativo) {
            alert('Este produto não está em leilão ativo');
            return;
        }
        
        // Produto está ativo, prosseguir com o lance
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=dar_lance&produto_id=${produtoId}&valor_lance=${valor}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('valor-lance').value = '';
                // Forçar atualização para todos os usuários
                atualizarDashboardLance(true);
            } else {
                alert('Erro ao dar lance: ' + data.error);
            }
        })
        .catch(err => alert('Erro: ' + err));
    })
    .catch(err => alert('Erro: ' + err));
}

// Função para incrementar lance
function incrementarLance(incremento) {
    const maiorLanceText = document.getElementById('maior-lance').innerText;
    const maiorLanceAtual = parseFloat(maiorLanceText.replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
    const valor = maiorLanceAtual + incremento;
    document.getElementById('valor-lance').value = valor.toFixed(2);
    
    // Não dá o lance automaticamente, apenas atualiza o valor no campo
}

// Função para retirar lance
function retirarLance(lanceId) {
    if (confirm('Deseja retirar este lance?')) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=retirar_lance&lance_id=${lanceId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Forçar atualização para todos os usuários
                atualizarDashboardLance(true);
            } else {
                alert('Erro ao retirar lance: ' + data.error);
            }
        })
        .catch(err => alert('Erro: ' + err));
    }
}

// Função para pesquisar sem reload
function pesquisarDinamicoLance(formElement, tableId) {
    formElement.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(formElement);
        const searchParams = new URLSearchParams(formData);
        
        fetch(`${formElement.action}?${searchParams.toString()}`)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Atualizar apenas a tabela específica
                document.querySelector(`#${tableId}`).innerHTML = doc.querySelector(`#${tableId}`).innerHTML;
                
                // Atualizar paginação
                const paginationElement = formElement.closest('div').querySelector('.pagination');
                if (paginationElement) {
                    paginationElement.innerHTML = doc.querySelector(`.${formElement.closest('div').className} .pagination`).innerHTML;
                }
            })
            .catch(err => console.error('Erro ao pesquisar:', err));
    });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar formulário de lance
    const bidForm = document.getElementById('bid-form');
    if (bidForm) {
        bidForm.addEventListener('submit', darLance);
    }
    
    // Inicializar pesquisa para itens vendidos
    const searchSoldForm = document.querySelector('.sold-items form');
    if (searchSoldForm) {
        pesquisarDinamicoLance(searchSoldForm, 'sold-items-table');
    }
    
    // Iniciar atualização automática
    atualizarDashboardLance();
});
