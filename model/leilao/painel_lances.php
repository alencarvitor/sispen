<?php
/**
 * Página de painel de lances do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Verificar se o usuário está logado
if (!$loggedIn) {
    redirect('login.php');
}

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Buscar produtos em leilão
$query = "SELECT p.ID_PRODUTO, p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM, 
         (SELECT MAX(l.VALOR_LANCE) FROM lance l WHERE l.PK_PRODUTO = p.ID_PRODUTO) as VALOR_ATUAL
         FROM produtos p
         WHERE p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido)
         AND p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido_lance_direto)
         ORDER BY p.ID_PRODUTO DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar lance
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dar_lance'])) {
    $produto_id = (int)$_POST['produto_id'];
    $valor_lance = (float)$_POST['valor_lance'];
    $user_id = $sessionManager->getUserId();
    
    if ($valor_lance <= 0) {
        $error = 'O valor do lance deve ser maior que zero.';
    } else {
        // Verificar valor atual do produto
        $query = "SELECT MAX(VALOR_LANCE) as valor_atual FROM lance WHERE PK_PRODUTO = :produto_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $valor_atual = $resultado['valor_atual'] ?? 0;
        
        if ($valor_lance <= $valor_atual) {
            $error = 'O valor do lance deve ser maior que o valor atual.';
        } else {
            // Registrar lance
            $query = "INSERT INTO lance (PK_USUARIO, PK_PRODUTO, VALOR_LANCE, DATA_HORA) 
                     VALUES (:user_id, :produto_id, :valor_lance, NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':produto_id', $produto_id);
            $stmt->bindParam(':valor_lance', $valor_lance);
            
            if ($stmt->execute()) {
                $success = 'Lance registrado com sucesso!';
            } else {
                $error = 'Erro ao registrar lance.';
            }
        }
    }
}
?>

<div class="row">
    <div class="col">
        <h1>Painel de Lances</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h2>Buscar Produtos</h2>
            </div>
            <div class="card-body">
                <form id="form-busca-produtos" class="search-box">
                    <input type="text" name="busca" class="form-control" placeholder="Digite o nome do produto...">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <h2>Produtos em Leilão</h2>
    </div>
</div>

<div id="resultado-busca">
    <div class="row">
        <?php if (count($produtos) > 0): ?>
            <?php foreach ($produtos as $produto): ?>
                <div class="col-4 mb-4">
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
                            <div class="lance-botoes">
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 5)">+R$ 5</button>
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 10)">+R$ 10</button>
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 20)">+R$ 20</button>
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 50)">+R$ 50</button>
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 100)">+R$ 100</button>
                                <button class="btn btn-secondary" onclick="darLance(<?php echo $produto['ID_PRODUTO']; ?>, 200)">+R$ 200</button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <h4>Histórico de Lances</h4>
                            <div class="lance-historico" id="lance-historico-<?php echo $produto['ID_PRODUTO']; ?>">
                                <div class="loader"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p>Nenhum produto em leilão no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <h2>Meus Itens Arrematados</h2>
        
        <?php
        // Buscar itens arrematados pelo usuário
        $user_id = $sessionManager->getUserId();
        $query = "SELECT p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM, pv.VALOR_LANCE, pv.DATA_HORA
                 FROM produto_vendido pv
                 JOIN produtos p ON pv.PK_PRODUTO = p.ID_PRODUTO
                 WHERE pv.PK_USER = :user_id
                 ORDER BY pv.DATA_HORA DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $itens_arrematados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <?php if (count($itens_arrematados) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Doador</th>
                            <th>Valor</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_arrematados as $item): ?>
                            <tr>
                                <td><?php echo $item['NOME_PRODUTO']; ?></td>
                                <td><?php echo $item['NOME_DOADOR']; ?></td>
                                <td>R$ <?php echo number_format($item['VALOR_LANCE'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($item['DATA_HORA'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Você ainda não arrematou nenhum item.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Iniciar atualizações automáticas quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    iniciarAtualizacoesAutomaticas();
});
</script>

<?php
// Incluir rodapé
include_once 'footer.php';
?>
