// Funções JavaScript para o Sistema de Leilão

// Função para inicializar o menu responsivo
function initMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
    
    // Fechar menu ao clicar em um link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
}

// Função para validar formulários
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
        
        // Validação específica para CPF
        if (input.id === 'cpf') {
            if (!validarCPF(input.value)) {
                input.classList.add('is-invalid');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Função para validar CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1+$/.test(cpf)) return false;
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let resto = soma % 11;
    let dv1 = resto < 2 ? 0 : 11 - resto;
    
    if (dv1 !== parseInt(cpf.charAt(9))) return false;
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    resto = soma % 11;
    let dv2 = resto < 2 ? 0 : 11 - resto;
    
    if (dv2 !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Função para formatar CPF durante digitação
function formatarCPF(input) {
    let cpf = input.value.replace(/\D/g, '');
    
    if (cpf.length > 11) {
        cpf = cpf.substring(0, 11);
    }
    
    if (cpf.length > 9) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else if (cpf.length > 6) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})/, '$1.$2.$3');
    } else if (cpf.length > 3) {
        cpf = cpf.replace(/(\d{3})(\d{3})/, '$1.$2');
    }
    
    input.value = cpf;
}

// Função para formatar telefone durante digitação
function formatarTelefone(input) {
    let telefone = input.value.replace(/\D/g, '');
    
    if (telefone.length > 11) {
        telefone = telefone.substring(0, 11);
    }
    
    if (telefone.length > 10) {
        telefone = telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (telefone.length > 6) {
        telefone = telefone.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (telefone.length > 2) {
        telefone = telefone.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    
    input.value = telefone;
}

// Função para mostrar mensagens de alerta
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Remover alerta após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Função para realizar requisições AJAX
function ajaxRequest(url, method = 'GET', data = null, callback = null) {
    const xhr = new XMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (callback) callback(response);
                } catch (e) {
                    console.error('Erro ao processar resposta:', e);
                    if (callback) callback({ success: false, message: 'Erro ao processar resposta do servidor.' });
                }
            } else {
                console.error('Erro na requisição:', xhr.status);
                if (callback) callback({ success: false, message: 'Erro na comunicação com o servidor.' });
            }
        }
    };
    
    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    } else {
        xhr.send();
    }
}

// Função para atualizar lances em tempo real
function atualizarLances(produtoId) {
    const lanceHistorico = document.getElementById(`lance-historico-${produtoId}`);
    const lanceValor = document.getElementById(`lance-valor-${produtoId}`);
    
    if (!lanceHistorico || !lanceValor) return;
    
    // Mostrar loader
    const loader = document.createElement('div');
    loader.className = 'loader';
    lanceHistorico.appendChild(loader);
    
    // Fazer requisição AJAX para obter lances atualizados
    ajaxRequest(`ajax/obter_lances.php?produto_id=${produtoId}`, 'GET', null, function(response) {
        // Remover loader
        loader.remove();
        
        if (response.success) {
            // Atualizar valor atual
            if (response.valor_atual) {
                lanceValor.textContent = `R$ ${response.valor_atual}`;
            }
            
            // Atualizar histórico de lances
            if (response.lances && response.lances.length > 0) {
                let html = '<table class="table">';
                html += '<thead><tr><th>Usuário</th><th>Valor</th><th>Data/Hora</th></tr></thead>';
                html += '<tbody>';
                
                response.lances.forEach(lance => {
                    html += `<tr>
                        <td>${lance.usuario}</td>
                        <td>R$ ${lance.valor}</td>
                        <td>${lance.data_hora}</td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                lanceHistorico.innerHTML = html;
            } else {
                lanceHistorico.innerHTML = '<p>Nenhum lance registrado.</p>';
            }
        } else {
            console.error('Erro ao atualizar lances:', response.message);
        }
    });
}

// Função para dar lance
function darLance(produtoId, valorBotao) {
    const lanceValorAtual = document.getElementById(`lance-valor-${produtoId}`);
    if (!lanceValorAtual) return;
    
    // Obter valor atual
    const valorAtual = parseFloat(lanceValorAtual.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));
    
    // Calcular novo valor
    const novoValor = valorAtual + valorBotao;
    
    // Mostrar loader
    const lanceContainer = document.querySelector(`.produto-card[data-id="${produtoId}"]`);
    const loader = document.createElement('div');
    loader.className = 'loader';
    lanceContainer.appendChild(loader);
    
    // Enviar lance via AJAX
    const data = `produto_id=${produtoId}&valor=${novoValor}`;
    ajaxRequest('ajax/dar_lance.php', 'POST', data, function(response) {
        // Remover loader
        loader.remove();
        
        if (response.success) {
            showAlert('Lance registrado com sucesso!', 'success');
            
            // Atualizar valor exibido
            lanceValorAtual.textContent = `R$ ${response.novo_valor}`;
            
            // Atualizar histórico de lances
            atualizarLances(produtoId);
        } else {
            showAlert(response.message || 'Erro ao registrar lance.', 'danger');
        }
    });
}

// Função para buscar produtos
function buscarProdutos(formId, resultadoId) {
    const form = document.getElementById(formId);
    const resultado = document.getElementById(resultadoId);
    
    if (!form || !resultado) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const termo = form.querySelector('input[name="busca"]').value.trim();
        
        // Mostrar loader
        resultado.innerHTML = '<div class="loader"></div>';
        
        // Fazer requisição AJAX
        ajaxRequest(`ajax/buscar_produtos.php?termo=${encodeURIComponent(termo)}`, 'GET', null, function(response) {
            if (response.success) {
                if (response.produtos && response.produtos.length > 0) {
                    let html = '<div class="row">';
                    
                    response.produtos.forEach(produto => {
                        html += `
                        <div class="col-4">
                            <div class="card produto-card" data-id="${produto.id}">
                                <img src="${produto.imagem}" alt="${produto.nome}" class="produto-img">
                                <div class="card-body">
                                    <h3>${produto.nome}</h3>
                                    <p>Doador: ${produto.doador}</p>
                                    <div class="lance-valor" id="lance-valor-${produto.id}">R$ ${produto.valor_atual}</div>
                                    <div class="lance-botoes">
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 5)">+R$ 5</button>
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 10)">+R$ 10</button>
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 20)">+R$ 20</button>
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 50)">+R$ 50</button>
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 100)">+R$ 100</button>
                                        <button class="btn btn-secondary" onclick="darLance(${produto.id}, 200)">+R$ 200</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    html += '</div>';
                    resultado.innerHTML = html;
                } else {
                    resultado.innerHTML = '<p>Nenhum produto encontrado.</p>';
                }
            } else {
                resultado.innerHTML = '<p>Erro ao buscar produtos.</p>';
            }
        });
    });
}

// Função para inicializar atualizações automáticas
function iniciarAtualizacoesAutomaticas() {
    // Obter todos os produtos na página
    const produtos = document.querySelectorAll('.produto-card');
    
    if (produtos.length > 0) {
        // Atualizar lances inicialmente
        produtos.forEach(produto => {
            const produtoId = produto.getAttribute('data-id');
            atualizarLances(produtoId);
        });
        
        // Configurar atualização periódica (a cada 5 segundos)
        setInterval(() => {
            produtos.forEach(produto => {
                const produtoId = produto.getAttribute('data-id');
                atualizarLances(produtoId);
            });
        }, 5000);
    }
}

// Função para gerenciar itens do leilão
function gerenciarItem(produtoId, acao) {
    // Mostrar loader
    const itemContainer = document.querySelector(`.item-leilao[data-id="${produtoId}"]`);
    if (!itemContainer) return;
    
    const loader = document.createElement('div');
    loader.className = 'loader';
    itemContainer.appendChild(loader);
    
    // Enviar ação via AJAX
    const data = `produto_id=${produtoId}&acao=${acao}`;
    ajaxRequest('ajax/gerenciar_item.php', 'POST', data, function(response) {
        // Remover loader
        loader.remove();
        
        if (response.success) {
            showAlert(response.message || 'Operação realizada com sucesso!', 'success');
            
            // Atualizar interface conforme a ação
            if (acao === 'retirar' || acao === 'vendido') {
                itemContainer.remove();
            } else if (acao === 'apagar_lance' && response.lances) {
                const lancesContainer = itemContainer.querySelector('.lances-recentes');
                if (lancesContainer) {
                    let html = '<h4>Lances Recentes</h4>';
                    
                    if (response.lances.length > 0) {
                        html += '<table class="table">';
                        html += '<thead><tr><th>Usuário</th><th>Valor</th><th>Data/Hora</th><th>Ação</th></tr></thead>';
                        html += '<tbody>';
                        
                        response.lances.forEach(lance => {
                            html += `<tr>
                                <td>${lance.usuario}</td>
                                <td>R$ ${lance.valor}</td>
                                <td>${lance.data_hora}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="gerenciarItem(${produtoId}, 'apagar_lance', ${lance.id})">Apagar</button></td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table>';
                    } else {
                        html += '<p>Nenhum lance registrado.</p>';
                    }
                    
                    lancesContainer.innerHTML = html;
                }
            }
        } else {
            showAlert(response.message || 'Erro ao realizar operação.', 'danger');
        }
    });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    initMenu();
    
    // Inicializar máscaras de input
    const cpfInputs = document.querySelectorAll('input[type="text"][id="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatarCPF(this);
        });
    });
    
    const telefoneInputs = document.querySelectorAll('input[type="text"][id="telefone"]');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatarTelefone(this);
        });
    });
    
    // Inicializar busca de produtos
    buscarProdutos('form-busca-produtos', 'resultado-busca');
    
    // Inicializar atualizações automáticas para lances
    iniciarAtualizacoesAutomaticas();
});
