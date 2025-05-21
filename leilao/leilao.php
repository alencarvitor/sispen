<?php
include 'session.php';
include 'db.php';

// Verificar se a sessão contém os dados esperados
if (!isset($_SESSION['user']['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

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
$sold_per_page = 5;
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

// Configuração de paginação e busca para Produtos Cadastrados
$all_page = isset($_GET['all_page']) ? (int)$_GET['all_page'] : 1;
$all_per_page = 5;
$all_offset = ($all_page - 1) * $all_per_page;
$search_all = isset($_GET['search_all']) ? trim($_GET['search_all']) : '';

$where_all = "WHERE p.em_leilao = 0 AND NOT EXISTS (SELECT 1 FROM itens_leiloados i WHERE i.produto_id = p.id)";
$params_all = [];
$types_all = '';
if ($search_all) {
    $where_all .= " AND (p.nome_produto LIKE ? OR p.nome_doador LIKE ?)";
    $search_term = "%$search_all%";
    $params_all = [$search_term, $search_term];
    $types_all = "ss";
}

$stmt = $conn->prepare("SELECT COUNT(*) as total 
                        FROM produtos p 
                        $where_all");
if ($search_all) {
    $stmt->bind_param($types_all, ...$params_all);
}
$stmt->execute();
$total_all = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$total_all_pages = ceil($total_all / $all_per_page);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Leilão</title>
    <link rel="stylesheet" href="css/leilao.css?v=<?php echo time(); ?>">
    <style>
        .search-box {
            margin-bottom: 10px;
        }
        .search-box input {
            padding: 5px;
            width: 100%;
            max-width: 300px;
        }
        .action-btn {
            margin: 2px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .pagination {
            margin-top: 10px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ccc;
            color: #333;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
        .pagination a:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.php">Painel</a></li>
            <li><a href="register.php">Cadastrar Usuário</a></li>
            <li><a href="cadastro_produto.php">Cadastrar Produto</a></li>
            <li><a href="leilao.php">Painel de Leilão</a></li>
            <li><a href="lance.php">Dar Lance</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>

    <div class="auction-status">
        <h2>Leilão: 
            <span id="status-text">
                <?php
                $status_text = [
                    'em_andamento' => 'Em Andamento',
                    'pausado' => 'Pausado',
                    'suspenso' => 'Suspenso',
                    'finalizado' => 'Finalizado'
                ];
                echo $status_text[$status];
                ?>
            </span>
        </h2>
        <select id="status-select" onchange="atualizarStatus()">
            <option value="em_andamento" <?php if ($status == 'em_andamento') echo 'selected'; ?>>Em Andamento</option>
            <option value="pausado" <?php if ($status == 'pausado') echo 'selected'; ?>>Pausado</option>
            <option value="suspenso" <?php if ($status == 'suspenso') echo 'selected'; ?>>Suspenso</option>
            <option value="finalizado" <?php if ($status == 'finalizado') echo 'selected'; ?>>Finalizado</option>
        </select>
    </div>

    <div class="dashboard">
        <!-- Tabela de Itens Vendidos (Esquerda) -->
        <div class="sold-items">
            <h3>Itens Vendidos</h3>
            <div class="search-box">
                <form method="GET" action="leilao.php">
                    <input type="text" name="search_sold" placeholder="Pesquisar por produto ou doador..." value="<?php echo htmlspecialchars($search_sold); ?>">
                    <input type="hidden" name="sold_page" value="1">
                    <input type="hidden" name="all_page" value="<?php echo $all_page; ?>">
                    <input type="hidden" name="search_all" value="<?php echo htmlspecialchars($search_all); ?>">
                    <button type="submit">Pesquisar</button>
                </form>
            </div>
            <table id="sold-items-table">
                <tr>
                    <th>Produto</th>
                    <th>Doador</th>
                    <th>Arrematante</th>
                    <th>Valor</th>
                    <th>Ação</th>
                </tr>
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
                            <td>
                                <button class='action-btn' onclick='retornarParaVenda({$row['id']})'>Retornar para Venda</button>
                            </td>
                          </tr>";
                }
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='5'>Nenhum item vendido.</td></tr>";
                }
                $stmt->close();
                ?>
            </table>
            <div class="pagination">
                <?php
                for ($i = 1; $i <= $total_sold_pages; $i++) {
                    $active = $i == $sold_page ? 'active' : '';
                    $params = http_build_query([
                        'sold_page' => $i,
                        'all_page' => $all_page,
                        'search_sold' => $search_sold,
                        'search_all' => $search_all
                    ]);
                    echo "<a href='leilao.php?$params' class='$active'>$i</a>";
                }
                ?>
            </div>
        </div>

        <!-- Centro: Imagem do Produto e Maior Lance -->
        <div class="current-item">
            <h3 id="produto-nome">
                <?php echo $produto_atual ? htmlspecialchars($produto_atual['nome_produto']) : 'Nenhum produto selecionado'; ?>
            </h3>
            <div class="item-image">
                <?php
                if ($produto_atual && $produto_atual['imagem']) {
                    echo "<img src='Uploads/{$produto_atual['imagem']}' alt='Produto'>";
                } else {
                    echo "<p>Sem imagem</p>";
                }
                ?>
            </div>
            <p id="doador-nome">
                Doador: <?php echo $produto_atual ? htmlspecialchars($produto_atual['nome_doador']) : '-'; ?>
            </p>
            <div class="highest-bid">
                <h4>Maior Lance</h4>
                <p>Arrematante: <span id="maior-arrematante"><?php echo $maior_arrematante ?: 'Nenhum lance'; ?></span></p>
                <p>Valor: <span id="maior-lance"><?php echo $maior_lance ? "R$ $maior_lance" : 'R$ 0,00'; ?></span></p>
            </div>
            <?php if ($produto_atual_id) { ?>
                <div class="action-buttons">
                    <button class="action-btn" onclick="retornarParaLista(<?php echo $produto_atual_id; ?>)">Retornar para Lista</button>
                    <button class="action-btn" onclick="marcarVendido(<?php echo $produto_atual_id; ?>)">Vendido</button>
                    <button class="action-btn" onclick="abrirParaLance(<?php echo $produto_atual_id; ?>)">Abrir para Lance</button>
                </div>
            <?php } ?>
        </div>

        <!-- Tabela de Produtos Cadastrados (Direita) -->
        <div class="all-items">
            <h3>Produtos Cadastrados</h3>
            <div class="search-box">
                <form method="GET" action="leilao.php">
                    <input type="text" name="search_all" placeholder="Pesquisar por produto ou doador..." value="<?php echo htmlspecialchars($search_all); ?>">
                    <input type="hidden" name="all_page" value="1">
                    <input type="hidden" name="sold_page" value="<?php echo $sold_page; ?>">
                    <input type="hidden" name="search_sold" value="<?php echo htmlspecialchars($search_sold); ?>">
                    <button type="submit">Pesquisar</button>
                </form>
            </div>
            <table id="all-items-table">
                <tr>
                    <th>Produto</th>
                    <th>Doador</th>
                    <th>Ação</th>
                </tr>
                <?php
                $query = "SELECT p.id, p.nome_produto, p.nome_doador, p.em_leilao 
                          FROM produtos p 
                          $where_all 
                          ORDER BY p.nome_produto ASC 
                          LIMIT ? OFFSET ?";
                $stmt = $conn->prepare($query);
                $params = array_merge($params_all, [$all_per_page, $all_offset]);
                $types = $types_all . "ii";
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $disabled = $row['em_leilao'] ? 'disabled' : '';
                    echo "<tr>
                            <td>{$row['nome_produto']}</td>
                            <td>{$row['nome_doador']}</td>
                            <td>
                                <button class='action-btn' onclick='selecionarProduto({$row['id']})' $disabled>
                                    Colocar em Leilão
                                </button>
                            </td>
                          </tr>";
                }
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='3'>Nenhum produto cadastrado.</td></tr>";
                }
                $stmt->close();
                ?>
            </table>
            <div class="pagination">
                <?php
                for ($i = 1; $i <= $total_all_pages; $i++) {
                    $active = $i == $all_page ? 'active' : '';
                    $params = http_build_query([
                        'sold_page' => $sold_page,
                        'all_page' => $i,
                        'search_sold' => $search_sold,
                        'search_all' => $search_all
                    ]);
                    echo "<a href='leilao.php?$params' class='$active'>$i</a>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Tabela de Lances (Inferior) -->
    <div class="bids">
        <h3>Histórico de Lances</h3>
        <table>
            <tr>
                <th>Arrematante</th>
                <th>Produto</th>
                <th>Valor</th>
                <th>Data</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT u.username, p.nome_produto, l.valor_lance, l.data_lance 
                                    FROM lances l 
                                    JOIN usuarios u ON l.usuario_id = u.id 
                                    JOIN produtos p ON l.produto_id = p.id 
                                    ORDER BY l.data_lance DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $valor = number_format($row['valor_lance'], 2, ',', '.');
                $data = date('d/m/Y H:i', strtotime($row['data_lance']));
                echo "<tr>
                        <td>{$row['username']}</td>
                        <td>{$row['nome_produto']}</td>
                        <td>R$ {$valor}</td>
                        <td>{$data}</td>
                      </tr>";
            }
            if ($result->num_rows == 0) {
                echo "<tr><td colspan='4'>Nenhum lance registrado.</td></tr>";
            }
            $stmt->close();
            ?>
        </table>
    </div>

    <script>
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
                    atualizarDashboard();
                } else {
                    alert('Erro ao atualizar status: ' + data.error);
                }
            })
            .catch(err => alert('Erro: ' + err));
        }

        function selecionarProduto(produtoId) {
            fetch('atualizar_leilao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=selecionar_produto&produto_id=${produtoId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    atualizarDashboard();
                } else {
                    alert('Erro ao selecionar produto: ' + data.error);
                }
            })
            .catch(err => alert('Erro: ' + err));
        }

        function retornarParaLista(produtoId) {
            if (confirm('Deseja retornar o produto para a lista de espera?')) {
                fetch('atualizar_leilao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=retornar_lista&produto_id=${produtoId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        atualizarDashboard();
                    } else {
                        alert('Erro ao retornar produto: ' + data.error);
                    }
                })
                .catch(err => alert('Erro: ' + err));
            }
        }

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
                        atualizarDashboard();
                    } else {
                        alert('Erro ao marcar como vendido: ' + data.error);
                    }
                })
                .catch(err => alert('Erro: ' + err));
            }
        }

        function abrirParaLance(produtoId) {
            if (confirm('Deseja abrir o produto para lances?')) {
                fetch('atualizar_leilao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=abrir_lance&produto_id=${produtoId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        atualizarDashboard();
                    } else {
                        alert('Erro ao abrir para lances: ' + data.error);
                    }
                })
                .catch(err => alert('Erro: ' + err));
            }
        }

        function retornarParaVenda(itemId) {
            if (confirm('Deseja retornar o item para venda?')) {
                fetch('atualizar_leilao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=retornar_venda&item_id=${itemId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        atualizarDashboard();
                    } else {
                        alert('Erro ao retornar para venda: ' + data.error);
                    }
                })
                .catch(err => alert('Erro: ' + err));
            }
        }

        function atualizarDashboard() {
            const url = new URL(window.location.href);
            fetch(url.pathname + url.search)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    document.querySelector('.dashboard').innerHTML = doc.querySelector('.dashboard').innerHTML;
                    document.querySelector('.bids').innerHTML = doc.querySelector('.bids').innerHTML;
                    document.getElementById('status-text').innerText = doc.getElementById('status-text').innerText;
                })
                .catch(err => console.error('Erro ao atualizar dashboard:', err));
        }

        // Atualizar maior lance em tempo real
        setInterval(() => {
            const produtoId = <?php echo $produto_atual_id ?: 'null'; ?>;
            if (produtoId && document.getElementById('status-text').innerText === 'Em Andamento') {
                fetch(`atualizar_leilao.php?action=buscar_maior_lance&produto_id=${produtoId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('maior-arrematante').innerText = data.arrematante || 'Nenhum lance';
                            document.getElementById('maior-lance').innerText = data.lance ? `R$ ${data.lance}` : 'R$ 0,00';
                            atualizarDashboard();
                        }
                    })
                    .catch(err => console.error('Erro ao atualizar lance:', err));
            }
        }, 5000);
    </script>
</body>
</html>