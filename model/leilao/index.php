<?php
/**
 * Página inicial do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Buscar produtos em leilão
$query = "SELECT p.ID_PRODUTO, p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM, 
         (SELECT MAX(l.VALOR_LANCE) FROM lance l WHERE l.PK_PRODUTO = p.ID_PRODUTO) as VALOR_ATUAL
         FROM produtos p
         WHERE p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido)
         AND p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido_lance_direto)
         ORDER BY p.ID_PRODUTO DESC
         LIMIT 6";

$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h1 class="text-center">Bem-vindo ao Sistema de Leilão</h1>
            </div>
            <div class="card-body">
                <p class="text-center">Participe dos nossos leilões e ajude a fazer a diferença!</p>
                
                <?php if (!$loggedIn): ?>
                <div class="text-center mt-3 mb-3">
                    <a href="login.php" class="btn btn-primary">Fazer Login</a>
                    <a href="cadastro.php" class="btn btn-secondary">Cadastrar-se</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <h2>Itens em Destaque</h2>
    </div>
</div>

<div class="row mt-2">
    <?php if (count($produtos) > 0): ?>
        <?php foreach ($produtos as $produto): ?>
            <div class="col-4">
                <div class="card produto-card" data-id="<?php echo $produto['ID_PRODUTO']; ?>">
                    <?php if (!empty($produto['IMAGEM'])): ?>
                        <img src="<?php echo $produto['IMAGEM']; ?>" alt="<?php echo $produto['NOME_PRODUTO']; ?>" class="produto-img">
                    <?php else: ?>
                        <img src="assets/img/no-image.jpg" alt="Sem imagem" class="produto-img">
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?php echo $produto['NOME_PRODUTO']; ?></h3>
                        <p>Doador: <?php echo $produto['NOME_DOADOR']; ?></p>
                        <div class="lance-valor" id="lance-valor-<?php echo $produto['ID_PRODUTO']; ?>">
                            R$ <?php echo number_format($produto['VALOR_ATUAL'] ?? 0, 2, ',', '.'); ?>
                        </div>
                        <?php if ($loggedIn): ?>
                            <div class="text-center mt-2">
                                <a href="painel_lances.php" class="btn btn-primary">Dar Lance</a>
                            </div>
                        <?php else: ?>
                            <div class="text-center mt-2">
                                <a href="login.php" class="btn btn-secondary">Faça login para dar lance</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col">
            <p>Nenhum item em leilão no momento.</p>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h2>Como Funciona</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <h3>1. Cadastre-se</h3>
                            <p>Crie sua conta para participar dos leilões.</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3>2. Escolha um Item</h3>
                            <p>Navegue pelos itens disponíveis e escolha os que deseja arrematar.</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3>3. Dê seu Lance</h3>
                            <p>Ofereça lances nos itens de seu interesse e acompanhe em tempo real.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'footer.php';

?>
