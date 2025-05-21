<?php
session_start();
include 'db.php';
include 'permissions.php';

// Verificar permissões (todos os usuários logados podem acessar)
verificarPermissao(['admin', 'leiloeiro', 'comprador']);

$user_id = $_SESSION['user_id'];

// Buscar status do leilão
$stmt = $conn->prepare("SELECT status, produto_id FROM leilao WHERE id = 1");
$stmt->execute();
$leilao = $stmt->get_result()->fetch_assoc();
$status = $leilao['status'];
$produto_atual_id = $leilao['produto_id'];
$stmt->close();

// Buscar produto atual (se houver)
$produto_atual = null;
if ($produto_atual_id) {
    $stmt = $conn->prepare("SELECT nome_produto, nome_doador, imagem FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $produto_atual_id);
    $stmt->execute();
    $produto_atual = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Buscar maior lance e arrematante
$maior_lance = null;
$maior_arrematante = null;
if ($produto_atual_id && $status == 'em_andamento') {
    $stmt = $conn->prepare("SELECT l.valor_lance, u.username 
                            FROM lances l 
                            JOIN usuarios u ON l.usuario_id = u.id 
                            WHERE l.produto_id = ? 
                            ORDER BY l.valor_lance DESC 
                            LIMIT 1");
    $stmt->bind_param("i", $produto_atual_id);
    $stmt->execute();
    $maior_lance = $stmt->get_result()->fetch_assoc();
    if ($maior_lance) {
        $maior_arrematante = $maior_lance['username'];
        $maior_lance = number_format($maior_lance['valor_lance'], 2, ',', '.');
    }
    $stmt->close();
}

// Configuração de paginação e busca para Itens Vendidos
$sold_page = isset($_GET['sold_page']) ? (int)$_GET['sold_page'] : 1;
$sold_per_page = 10;
$sold_offset = ($sold_page - 1) * $sold_per_page;
$search_sold = isset($_GET['search_sold']) ? trim($_GET['search_sold']) : '';

$where_sold = '';
$params_sold = [];
$types_sold = '';
if ($search_sold) {
    $where_sold = "WHERE p.nome_produto LIKE ? OR p.nome_doador LIKE ?";
    $search_term = "%$search_sold%";
    $params_sold = [$search_term, $search_term];
    $types_sold = "ss";
}

$stmt = $conn->prepare("SELECT COUNT(*) as total 
                        FROM itens_leiloados i 
                        JOIN produtos p ON i.produto_id = p.id 
                        $where_sold");
if ($search_sold) {
    $stmt->bind_param($types_sold, ...$params_sold);
}
$stmt->execute();
$total_sold = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$total_sold_pages = ceil($total_sold / $sold_per_page);

$page_title = 'Painel de Lances';
include 'header.php';
?>

<div class="row">
    <!-- Produto Atual e Lance -->
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h2 id="produto-nome">
                    <?php echo $produto_atual ? htmlspecialchars($produto_atual['nome_produto']) : 'Nenhum produto em leilão'; ?>
                </h2>
            </div>
            <div class="card-body">
                <div class="item-image">
                    <?php
                    if ($produto_atual && $produto_atual['imagem']) {
                        echo "<img src='Uploads/{$produto_atual['imagem']}' alt='Produto'>";
                    } else {
                        echo "<p>Sem imagem</p>";
                    }
                    ?>
                </div>
                <p id="doador-nome" class="mb-3">
                    <strong>Doador:</strong> <?php echo $produto_atual ? htmlspecialchars($produto_atual['nome_doador']) : '-'; ?>
                </p>
                <div class="highest-bid mb-4">
                    <h4>Maior Lance</h4>
                    <p><strong>Arrematante:</strong> <span id="maior-arrematante"><?php echo $maior_arrematante ?: 'Nenhum lance'; ?></span></p>
                    <p><strong>Valor:</strong> <span id="maior-lance"><?php echo $maior_lance ? "R$ $maior_lance" : 'R$ 0,00'; ?></span></p>
                </div>
                
                <?php if ($produto_atual_id && $status == 'em_andamento') { ?>
                    <div class="bid-section">
                        <h4>Dar Lance</h4>
                        <form id="bid-form" onsubmit="darLance(event)">
                            <div class="form-group">
                                <label for="valor-lance">Valor do Lance</label>
                                <input type="number" class="bid-input" id="valor-lance" min="0" step="0.01" placeholder="Digite seu lance" required>
                            </div>
                            <button type="submit" class="btn">Dar Lance</button>
                            
                            <div class="bid-buttons mt-3">
                                <button type="button" class="btn" onclick="incrementarLance(5)">+5</button>
                                <button type="button" class="btn" onclick="incrementarLance(10)">+10</button>
                                <button type="button" class="btn" onclick="incrementarLance(15)">+15</button>
                                <button type="button" class="btn" onclick="incrementarLance(20)">+20</button>
                                <button type="button" class="btn" onclick="incrementarLance(50)">+50</button>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-info">
                        <?php if (!$produto_atual_id): ?>
                            <p>Não há produto em leilão no momento.</p>
                        <?php else: ?>
                            <p>O leilão está <?php echo $status == 'pausado' ? 'pausado' : 'finalizado'; ?> no momento.</p>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!-- Tabela de Itens Vendidos -->
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h3>Últimos Itens Vendidos</h3>
            </div>
            <div class="card-body">
                <div class="search-box mb-3">
                    <form method="GET" action="lance.php">
                        <div class="form-group">
                            <input type="text" name="search_sold" placeholder="Pesquisar por produto ou doador..." value="<?php echo htmlspecialchars($search_sold); ?>">
                            <input type="hidden" name="sold_page" value="1">
                            <button type="submit" class="btn">Pesquisar</button>
                        </div>
                    </form>
                </div>
                
                <table id="sold-items-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Doador</th>
                            <th>Arrematante</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT i.id, p.nome_produto, p.nome_doador, u.username, i.valor_arremate 
                                FROM itens_leiloados i 
                                JOIN produtos p ON i.produto_id = p.id 
                                JOIN usuarios u ON i.usuario_id = u.id 
                                $where_sold 
                                ORDER BY i.data_arremate DESC 
                                LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($query);
                        $params = array_merge($params_sold, [$sold_per_page, $sold_offset]);
                        $types = $types_sold . "ii";
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $valor = number_format($row['valor_arremate'], 2, ',', '.');
                            echo "<tr>
                                    <td>{$row['nome_produto']}</td>
                                    <td>{$row['nome_doador']}</td>
                                    <td>{$row['username']}</td>
                                    <td>R$ {$valor}</td>
                                </tr>";
                        }
                        if ($result->num_rows == 0) {
                            echo "<tr><td colspan='4'>Nenhum item vendido.</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <?php
                    for ($i = 1; $i <= $total_sold_pages; $i++) {
                        $active = $i == $sold_page ? 'active' : '';
                        $params = http_build_query(['sold_page' => $i, 'search_sold' => $search_sold]);
                        echo "<a href='lance.php?$params' class='$active'>$i</a>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Itens Arrematados pelo Usuário -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Itens Arrematados por Você</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Doador</th>
                    <th>Valor</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT p.nome_produto, p.nome_doador, i.valor_arremate, i.data_arremate 
                                        FROM itens_leiloados i 
                                        JOIN produtos p ON i.produto_id = p.id 
                                        WHERE i.usuario_id = ? 
                                        ORDER BY i.data_arremate DESC");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $valor = number_format($row['valor_arremate'], 2, ',', '.');
                    $data = date('d/m/Y H:i', strtotime($row['data_arremate']));
                    echo "<tr>
                            <td>{$row['nome_produto']}</td>
                            <td>{$row['nome_doador']}</td>
                            <td>R$ {$valor}</td>
                            <td>{$data}</td>
                        </tr>";
                }
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='4'>Nenhum item arrematado.</td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function darLance(event) {
    event.preventDefault();
    const valor = parseFloat(document.getElementById('valor-lance').value);
    if (isNaN(valor) || valor <= 0) {
        mostrarNotificacao('Por favor, insira um valor válido.', 'error');
        return;
    }
    
    fetch('atualizar_leilao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=dar_lance&produto_id=<?php echo $produto_atual_id; ?>&valor_lance=${valor}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('valor-lance').value = '';
            mostrarNotificacao('Lance registrado com sucesso!');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            mostrarNotificacao('Erro ao dar lance: ' + data.error, 'error');
        }
    })
    .catch(err => mostrarNotificacao('Erro: ' + err, 'error'));
}

function incrementarLance(incremento) {
    const maiorLanceAtual = '<?php echo $maior_lance ? str_replace(',', '.', str_replace('.', '', $maior_lance)) : '0'; ?>';
    const valor = parseFloat(maiorLanceAtual) + incremento;
    document.getElementById('valor-lance').value = valor.toFixed(2);
}

// Atualizar maior lance a cada 5 segundos
setInterval(() => {
    const produtoId = <?php echo $produto_atual_id ?: 'null'; ?>;
    if (produtoId && '<?php echo $status; ?>' === 'em_andamento') {
        fetch(`atualizar_leilao.php?action=buscar_maior_lance&produto_id=${produtoId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('maior-arrematante').innerText = data.arrematante || 'Nenhum lance';
                    document.getElementById('maior-lance').innerText = data.lance ? `R$ ${data.lance}` : 'R$ 0,00';
                }
            })
            .catch(err => console.error('Erro ao atualizar maior lance:', err));
    }
}, 5000);
</script>

<?php include 'footer.php'; ?>
