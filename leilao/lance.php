<?php
include 'session.php';
include 'db.php';

// Verificar se a sessão contém os dados esperados
if (!isset($_SESSION['user']['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Lances</title>
    <link rel="stylesheet" href="css/leilao.css?v=<?php echo time(); ?>">
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }
        .current-item, .sold-items {
            width: 48%;
        }
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
        .bid-input {
            padding: 5px;
            width: 100px;
            margin-right: 10px;
        }
        .bid-buttons button {
            margin: 5px;
        }
        .user-bids, .user-won-items {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="leilao.php">Painel de Leilão</a></li>
            <li><a href="lance.php">Dar Lance</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>

    <div class="container">
        <!-- Produto Atual e Lance -->
        <div class="current-item">
            <h3 id="produto-nome">
                <?php echo $produto_atual ? htmlspecialchars($produto_atual['nome_produto']) : 'Nenhum produto em leilão'; ?>
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
            <?php if ($produto_atual_id && $status == 'em_andamento') { ?>
                <div class="bid-section">
                    <h4>Dar Lance</h4>
                    <form id="bid-form" onsubmit="darLance(event)">
                        <input type="number" class="bid-input" id="valor-lance" min="0" step="0.01" placeholder="Digite seu lance" required>
                        <button type="submit" class="action-btn">Dar Lance</button>
                    </form>
                    <div class="bid-buttons">
                        <button class="action-btn" onclick="incrementarLance(5)">+5</button>
                        <button class="action-btn" onclick="incrementarLance(10)">+10</button>
                        <button class="action-btn" onclick="incrementarLance(15)">+15</button>
                        <button class="action-btn" onclick="incrementarLance(20)">+20</button>
                        <button class="action-btn" onclick="incrementarLance(50)">+50</button>
                    </div>
                </div>
                <!-- Tabela de Lances do Usuário -->
                <div class="user-bids">
                    <h4>Seus Lances</h4>
                    <table>
                        <tr>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Ação</th>
                        </tr>
                        <?php
                        $stmt = $conn->prepare("SELECT id, valor_lance, data_lance 
                                                FROM lances 
                                                WHERE produto_id = ? AND usuario_id = ? 
                                                ORDER BY data_lance DESC");
                        $stmt->bind_param("ii", $produto_atual_id, $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $valor = number_format($row['valor_lance'], 2, ',', '.');
                            $data = date('d/m/Y H:i', strtotime($row['data_lance']));
                            echo "<tr>
                                    <td>R$ {$valor}</td>
                                    <td>{$data}</td>
                                    <td>
                                        <button class='action-btn' onclick='retirarLance({$row['id']})'>Retirar</button>
                                    </td>
                                  </tr>";
                        }
                        if ($result->num_rows == 0) {
                            echo "<tr><td colspan='3'>Nenhum lance registrado.</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </table>
                </div>
            <?php } ?>
        </div>

        <!-- Tabela de Itens Vendidos -->
        <div class="sold-items">
            <h3>Últimos 10 Itens Vendidos</h3>
            <div class="search-box">
                <form method="GET" action="lance.php">
                    <input type="text" name="search_sold" placeholder="Pesquisar por produto ou doador..." value="<?php echo htmlspecialchars($search_sold); ?>">
                    <input type="hidden" name="sold_page" value="1">
                    <button type="submit">Pesquisar</button>
                </form>
            </div>
            <table id="sold-items-table">
                <tr>
                    <th>Produto</th>
                    <th>Doador</th>
                    <th>Arrematante</th>
                    <th>Valor</th>
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
                          </tr>";
                }
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='4'>Nenhum item vendido.</td></tr>";
                }
                $stmt->close();
                ?>
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

    <!-- Itens Arrematados pelo Usuário -->
    <div class="user-won-items">
        <h3>Itens Arrematados por Você</h3>
        <table>
            <tr>
                <th>Produto</th>
                <th>Doador</th>
                <th>Valor</th>
                <th>Data</th>
            </tr>
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
        </table>
    </div>

    <script>
        function darLance(event) {
            event.preventDefault();
            const valor = parseFloat(document.getElementById('valor-lance').value);
            if (isNaN(valor) || valor <= 0) {
                alert('Por favor, insira um valor válido.');
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
                    atualizarDashboard();
                } else {
                    alert('Erro ao dar lance: ' + data.error);
                }
            })
            .catch(err => alert('Erro: ' + err));
        }

        function incrementarLance(incremento) {
            const maiorLanceAtual = '<?php echo $maior_lance ? str_replace(',', '.', str_replace('.', '', $maior_lance)) : '0'; ?>';
            const valor = parseFloat(maiorLanceAtual) + incremento;
            document.getElementById('valor-lance').value = valor.toFixed(2);
            darLance(new Event('submit'));
        }

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
                        atualizarDashboard();
                    } else {
                        alert('Erro ao retirar lance: ' + data.error);
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
                    document.querySelector('.container').innerHTML = doc.querySelector('.container').innerHTML;
                    document.querySelector('.user-won-items').innerHTML = doc.querySelector('.user-won-items').innerHTML;
                })
                .catch(err => console.error('Erro ao atualizar dashboard:', err));
        }

        // Atualizar maior lance em tempo real
        setInterval(() => {
            const produtoId = <?php echo $produto_atual_id ?: 'null'; ?>;
            if (produtoId && '<?php echo $status; ?>' === 'em_andamento') {
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