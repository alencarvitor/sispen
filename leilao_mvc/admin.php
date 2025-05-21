<?php
session_start();
include 'db.php';
include 'permissions.php';

// Verificar permissões (admin)
verificarPermissao(['admin']);

$page_title = 'Painel Administrativo';
include 'header.php';
?>

<div class="card">
    <div class="card-header">
        <h1>Painel Administrativo</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Gerenciamento de Usuários</h3>
                    </div>
                    <div class="card-body">
                        <p>Adicione, edite ou remova usuários do sistema.</p>
                        <a href="register.php" class="btn">Adicionar Novo Usuário</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Gerenciamento de Produtos</h3>
                    </div>
                    <div class="card-body">
                        <p>Cadastre novos produtos para leilão.</p>
                        <a href="cadastro_produto.php" class="btn">Cadastrar Produto</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Painel de Leilão</h3>
                    </div>
                    <div class="card-body">
                        <p>Gerencie os leilões ativos e histórico de vendas.</p>
                        <a href="leilao.php" class="btn">Acessar Painel de Leilão</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Área de Lances</h3>
                    </div>
                    <div class="card-body">
                        <p>Visualize e participe dos leilões ativos.</p>
                        <a href="lance.php" class="btn">Acessar Área de Lances</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
