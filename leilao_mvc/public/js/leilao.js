// Função para atualizar o dashboard sem reload
function atualizarDashboard(forceUpdate = false) {
    // Atualizar maior lance e arrematante
    const produtoId = document.getElementById('produto-atual-id')?.value;
    if (produtoId) {
        // Verificar se o produto ainda está em leilão
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
            
            // Continuar com a atualização
            if (document.getElementById('status-text').innerText.includes('Em Andamento') || forceUpdate) {
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
            
            // Atualizar histórico de lances e tabelas
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Atualizar histórico de lances
                    if (document.querySelector('.bids table')) {
                        document.querySelector('.bids table').innerHTML = doc.querySelector('.bids table').innerHTML;
                    }
                    
                    // Atualizar tabelas de itens vendidos e produtos cadastrados
                    if (document.querySelector('#sold-items-table')) {
                        document.querySelector('#sold-items-table').innerHTML = doc.querySelector('#sold-items-table').innerHTML;
                    }
                    
                    if (document.querySelector('#all-items-table')) {
                        document.querySelector('#all-items-table').innerHTML = doc.querySelector('#all-items-table').innerHTML;
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
setInterval(atualizarDashboard, 5000);

// Função para atualizar status sem reload
function atualizarStatus() {
    const status = document.getElementById('status-select').value;
    fetch('atualizar_leilao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=atualizar_status&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('status-text').innerText = {
                'em_andamento': 'Em Andamento',
                'pausado': 'Pausado',
                'suspenso': 'Suspenso',
                'finalizado': 'Finalizado'
            }[status];
            // Forçar atualização para todos os usuários
            atualizarDashboard(true);
        } else {
            alert('Erro ao atualizar status: ' + data.error);
        }
    })
    .catch(err => alert('Erro: ' + err));
}

// Função para selecionar produto sem reload
function selecionarProduto(produtoId) {
    fetch('atualizar_leilao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=selecionar_produto&produto_id=${produtoId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Forçar atualização para todos os usuários
            atualizarDashboard(true);
        } else {
            alert('Erro ao selecionar produto: ' + data.error);
        }
    })
    .catch(err => alert('Erro: ' + err));
}

// Função para retornar produto para lista sem reload
function retornarParaLista(produtoId) {
    if (confirm('Deseja retornar o produto para a lista de espera?')) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=retornar_para_lista&produto_id=${produtoId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Forçar atualização para todos os usuários
                atualizarDashboard(true);
            } else {
                alert('Erro ao retornar produto: ' + data.error);
            }
        })
        .catch(err => alert('Erro: ' + err));
    }
}

// Função para marcar produto como vendido sem reload
function marcarVendido(produtoId) {
    if (confirm('Deseja marcar o produto como vendido?')) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=marcar_vendido&produto_id=${produtoId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Forçar atualização para todos os usuários
                atualizarDashboard(true);
            } else {
                alert('Erro ao marcar como vendido: ' + data.error);
            }
        })
        .catch(err => alert('Erro: ' + err));
    }
}

// Função para retornar item para venda sem reload
function retornarParaVenda(itemId) {
    if (confirm('Deseja retornar o item para venda?')) {
        fetch('atualizar_leilao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=retornar_para_venda&item_id=${itemId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Forçar atualização para todos os usuários
                atualizarDashboard(true);
            } else {
                alert('Erro ao retornar item para venda: ' + data.error);
            }
        })
        .catch(err => alert('Erro: ' + err));
    }
}

// Função para pesquisar sem reload
function pesquisarDinamico(formElement, tableId) {
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

// Inicializar pesquisa dinâmica quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar pesquisa para itens vendidos
    const searchSoldForm = document.querySelector('.sold-items form');
    if (searchSoldForm) {
        pesquisarDinamico(searchSoldForm, 'sold-items-table');
    }
    
    // Inicializar pesquisa para produtos cadastrados
    const searchAllForm = document.querySelector('.all-items form');
    if (searchAllForm) {
        pesquisarDinamico(searchAllForm, 'all-items-table');
    }
    
    // Iniciar atualização automática
    atualizarDashboard();
});
