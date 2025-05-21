  

    <?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="css/cadastrar_produto.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.php">Painel</a></li>
            <li><a href="register.php">Cadastrar Usuário</a></li>
            <li><a href="cadastro_produto.php">Cadastrar Produto</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
    <div class="container produto-container">
        <h2 id="formTitle">Cadastro de Produto</h2>
        <form id="formProduto" enctype="multipart/form-data">
            <input type="hidden" name="id" id="produtoId">
            <input type="text" name="nome_produto" id="nomeProduto" placeholder="Nome do Produto" required>
            <input type="text" name="nome_doador" id="nomeDoador" placeholder="Nome do Doador" required>
            <input type="number" step="0.01" name="valor_produto" id="valorProduto" placeholder="Valor do Produto (opcional)">
            <input type="number" step="0.01" name="valor_venda" id="valorVenda" placeholder="Valor de Venda (opcional)">
            <input type="file" name="imagem" id="imagemProduto">
            <button type="submit" id="submitButton">Cadastrar</button>
            <button type="reset" onclick="resetForm()">Limpar</button>
        </form>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Pesquisar por produto ou doador...">
            <button onclick="carregarTabela(1)">Pesquisar</button>
        </div>
        <div id="tabelaProdutos"></div>
    </div>

    <script>
        let currentPage = 1;

        function carregarTabela(page = 1) {
            currentPage = page;
            const search = document.getElementById('searchInput').value;
            const url = `listar_produtos.php?page=${page}&search=${encodeURIComponent(search)}`;
            console.log('Carregando tabela:', url);
            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error(`Erro ao carregar a tabela: ${res.status} ${res.statusText}`);
                    return res.text();
                })
                .then(html => {
                    document.getElementById('tabelaProdutos').innerHTML = html;
                })
                .catch(err => {
                    console.error('Erro:', err);
                    document.getElementById('tabelaProdutos').innerHTML = '<p>Erro ao carregar a lista de produtos.</p>';
                });
        }

        function resetForm() {
            document.getElementById('formProduto').reset();
            document.getElementById('produtoId').value = '';
            document.getElementById('formTitle').innerText = 'Cadastro de Produto';
            document.getElementById('submitButton').innerText = 'Cadastrar';
        }

        function deletarProduto(id) {
            if (confirm('Tem certeza que deseja deletar este produto?')) {
                console.log('Enviando requisição para apagar_produto.php com ID:', id);
                fetch('apagar_produto.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(res => {
                    console.log('Resposta recebida:', res);
                    if (!res.ok) throw new Error(`Erro ao deletar produto: ${res.status} ${res.statusText}`);
                    return res.text();
                })
                .then(response => {
                    console.log('Mensagem do servidor:', response);
                    alert(response);
                    carregarTabela(currentPage);
                })
                .catch(err => {
                    console.error('Erro:', err);
                    alert('Erro ao deletar produto: ' + err.message);
                });
            }
        }

        function editarProduto(id) {
            console.log('Carregando dados do produto com ID:', id);
            fetch(`atualizar_produto.php?id=${id}`)
                .then(res => {
                    console.log('Resposta recebida:', res);
                    if (!res.ok) throw new Error(`Erro ao carregar dados do produto: ${res.status} ${res.statusText}`);
                    return res.json();
                })
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    console.log('Dados recebidos:', data);
                    document.getElementById('produtoId').value = data.id;
                    document.getElementById('nomeProduto').value = data.nome_produto;
                    document.getElementById('nomeDoador').value = data.nome_doador;
                    document.getElementById('valorProduto').value = data.valor_produto || '';
                    document.getElementById('valorVenda').value = data.valor_venda || '';
                    document.getElementById('formTitle').innerText = 'Atualizar Produto';
                    document.getElementById('submitButton').innerText = 'Atualizar';
                })
                .catch(err => {
                    console.error('Erro:', err);
                    alert('Erro ao carregar dados do produto: ' + err.message);
                });
        }

        document.getElementById('formProduto').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = document.getElementById('produtoId').value;
            const url = id ? 'atualizar_produto.php' : 'processar_produto.php';
            console.log('Enviando formulário para:', url);

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                console.log('Resposta recebida:', res);
                if (!res.ok) throw new Error(`Erro ao salvar produto: ${res.status} ${res.statusText}`);
                return res.text();
            })
            .then(response => {
                console.log('Mensagem do servidor:', response);
                alert(response);
                resetForm();
                carregarTabela(currentPage);
            })
            .catch(err => {
                console.error('Erro:', err);
                alert('Erro ao salvar produto: ' + err.message);
            });
        });

        // Atualizar tabela ao pressionar Enter na busca
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                carregarTabela(1);
            }
        });

        carregarTabela();
    </script>
</body>
</html>