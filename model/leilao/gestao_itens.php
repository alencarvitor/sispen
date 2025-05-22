<?php
/**
 * Página de gestão de itens em leilão do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Verificar se o usuário está logado e tem permissão
if (!$loggedIn) {
    redirect('login.php');
}

// Verificar permissão (apenas administrador e leiloeiro)
if (!$sessionManager->hasPermission(['administrador', 'leiloeiro'])) {
    redirect('index.php');
}

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Processar ações
$error = '';
$success = '';

// Retirar item
if (isset($_POST['retirar_item'])) {
    $produto_id = (int)$_POST['produto_id'];
    
    // Excluir lances do produto
    $query = "DELETE FROM lance WHERE PK_PRODUTO = :produto_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':produto_id', $produto_id);
    
    if ($stmt->execute()) {
        $success = 'Item retirado do leilão com sucesso!';
    } else {
        $error = 'Erro ao retirar item do leilão.';
    }
}

// Marcar como vendido
if (isset($_POST['marcar_vendido'])) {
    $produto_id = (int)$_POST['produto_id'];
    
    // Buscar lance mais alto
    $query = "SELECT l.PK_USUARIO, l.VALOR_LANCE 
             FROM lance l 
             WHERE l.PK_PRODUTO = :produto_id 
             ORDER BY l.VALOR_LANCE DESC 
             LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':produto_id', $produto_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $lance = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Registrar venda
        $query = "INSERT INTO produto_vendido (PK_USER, PK_PRODUTO, VALOR_LANCE, DATA_HORA) 
                 VALUES (:user_id, :produto_id, :valor_lance, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $lance['PK_USUARIO']);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->bindParam(':valor_lance', $lance['VALOR_LANCE']);
        
        if ($stmt->execute()) {
            $success = 'Item marcado como vendido com sucesso!';
        } else {
            $error = 'Erro ao marcar item como vendido.';
        }
    } else {
        $error = 'Não há lances para este item.';
    }
}

// Apagar lance
if (isset($_POST['apagar_lance'])) {
    $lance_id = (int)$_POST['lance_id'];
    
    $query = "DELETE FROM lance WHERE ID_LANCE = :lance_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':lance_id', $lance_id);
    
    if ($stmt->execute()) {
        $success = 'Lance apagado com sucesso!';
    } else {
        $error = 'Erro ao apagar lance.';
    }
}

// Adicionar novo produto
if (isset($_POST['adicionar_produto'])) {
    $nome_produto = sanitize($_POST['nome_produto']);
    $nome_doador = sanitize($_POST['nome_doador']);
    $imagem = sanitize($_POST['imagem']);
    
    $query = "INSERT INTO produtos (NOME_PRODUTO, NOME_DOADOR, IMAGEM) 
             VALUES (:nome_produto, :nome_doador, :imagem)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nome_produto', $nome_produto);
    $stmt->bindParam(':nome_doador', $nome_doador);
    $stmt->bindParam(':imagem', $imagem);
    
    if ($stmt->execute()) {
        $success = 'Produto adicionado com sucesso!';
    } else {
        $error = 'Erro ao adicionar produto.';
    }
}

// Buscar produtos em leilão
$query = "SELECT p.ID_PRODUTO, p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM, 
         (SELECT MAX(l.VALOR_LANCE) FROM lance l WHERE l.PK_PRODUTO = p.ID_PRODUTO) as VALOR_ATUAL
         FROM produtos p
         WHERE p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido)
         AND p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido_lance_direto)
         ORDER BY p.ID_PRODUTO DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$produtos_leilao = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todos os produtos disponíveis
$query = "SELECT p.ID_PRODUTO, p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM
         FROM produtos p
         ORDER BY p.ID_PRODUTO DESC
         LIMIT 10";

$stmt = $db->prepare($query);
$stmt->execute();
$todos_produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col">
        <h1>Gestão de Itens em Leilão</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h2>Itens em Leilão</h2>
            </div>
            <div class="card-body">
                <?php if (count($produtos_leilao) > 0): ?>
                    <?php foreach ($produtos_leilao as $produto): ?>
                        <div class="card mb-3 item-leilao" data-id="<?php echo $produto['ID_PRODUTO']; ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <?php if (!empty($produto['IMAGEM'])): ?>
                                            <img src="<?php echo $produto['IMAGEM']; ?>" alt="<?php echo $produto['NOME_PRODUTO']; ?>" class="img-fluid">
                                        <?php else: ?>
                                            <img src="assets/img/no-image.jpg" alt="Sem imagem" class="img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-8">
                                        <h3><?php echo $produto['NOME_PRODUTO']; ?></h3>
                                        <p>Doador: <?php echo $produto['NOME_DOADOR']; ?></p>
                                        <p>Valor Atual: R$ <?php echo number_format($produto['VALOR_ATUAL'] ?? 0, 2, ',', '.'); ?></p>
                                        
                                        <div class="btn-group">
                                            <form method="POST" class="mr-2">
                                                <input type="hidden" name="produto_id" value="<?php echo $produto['ID_PRODUTO']; ?>">
                                                <button type="submit" name="retirar_item" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja retirar este item do leilão?')">Retirar Item</button>
                                            </form>
                                            
                                            <form method="POST">
                                                <input type="hidden" name="produto_id" value="<?php echo $produto['ID_PRODUTO']; ?>">
                                                <button type="submit" name="marcar_vendido" class="btn btn-success" onclick="return confirm('Tem certeza que deseja marcar este item como vendido?')">Marcar como Vendido</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="lances-recentes mt-3">
                                    <h4>Lances Recentes</h4>
                                    <?php
                                    // Buscar lances recentes
                                    $query = "SELECT l.ID_LANCE, u.NOME_USER, u.SOBRENOME_USER, l.VALOR_LANCE, l.DATA_HORA
                                             FROM lance l
                                             JOIN usuario u ON l.PK_USUARIO = u.ID_USER
                                             WHERE l.PK_PRODUTO = :produto_id
                                             ORDER BY l.VALOR_LANCE DESC
                                             LIMIT 5";
                                    
                                    $stmt = $db->prepare($query);
                                    $stmt->bindParam(':produto_id', $produto['ID_PRODUTO']);
                                    $stmt->execute();
                                    $lances = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    
                                    <?php if (count($lances) > 0): ?>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Usuário</th>
                                                    <th>Valor</th>
                                                    <th>Data/Hora</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($lances as $lance): ?>
                                                    <tr>
                                                        <td><?php echo $lance['NOME_USER'] . ' ' . $lance['SOBRENOME_USER']; ?></td>
                                                        <td>R$ <?php echo number_format($lance['VALOR_LANCE'], 2, ',', '.'); ?></td>
                                                        <td><?php echo date('d/m/Y H:i:s', strtotime($lance['DATA_HORA'])); ?></td>
                                                        <td>
                                                            <form method="POST">
                                                                <input type="hidden" name="lance_id" value="<?php echo $lance['ID_LANCE']; ?>">
                                                                <button type="submit" name="apagar_lance" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja apagar este lance?')">Apagar</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p>Nenhum lance registrado para este item.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum item em leilão no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h2>Todos os Produtos</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <form id="form-busca-todos-produtos" class="search-box">
                        <input type="text" name="busca" class="form-control" placeholder="Digite o nome do produto...">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>
                
                <div class="mb-4">
                    <h3>Adicionar Novo Produto</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="nome_produto">Nome do Produto:</label>
                            <input type="text" id="nome_produto" name="nome_produto" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nome_doador">Nome do Doador:</label>
                            <input type="text" id="nome_doador" name="nome_doador" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="imagem">URL da Imagem:</label>
                            <input type="text" id="imagem" name="imagem" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="adicionar_produto" class="btn btn-primary">Adicionar Produto</button>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Doador</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todos_produtos as $produto): ?>
                                <tr>
                                    <td><?php echo $produto['ID_PRODUTO']; ?></td>
                                    <td><?php echo $produto['NOME_PRODUTO']; ?></td>
                                    <td><?php echo $produto['NOME_DOADOR']; ?></td>
                                    <td>
                                        <?php
                                        // Verificar status do produto
                                        $query = "SELECT 'vendido' as status FROM produto_vendido WHERE PK_PRODUTO = :produto_id
                                                 UNION
                                                 SELECT 'vendido_direto' as status FROM produto_vendido_lance_direto WHERE PK_PRODUTO = :produto_id";
                                        
                                        $stmt = $db->prepare($query);
                                        $stmt->bindParam(':produto_id', $produto['ID_PRODUTO']);
                                        $stmt->execute();
                                        
                                        if ($stmt->rowCount() > 0) {
                                            $status = $stmt->fetch(PDO::FETCH_ASSOC);
                                            echo '<span class="badge bg-success">Vendido</span>';
                                        } else {
                                            echo '<span class="badge bg-primary">Disponível</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <ul>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li class="active"><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'footer.php';
?>
