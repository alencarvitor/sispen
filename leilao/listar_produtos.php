<?php
include 'db.php';

// Configuração de paginação
$itens_por_pagina = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Garante que a página não seja menor que 1
$offset = ($page - 1) * $itens_por_pagina;

// Parâmetro de busca
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Preparar a consulta SQL com busca e paginação
$sql = "SELECT id, nome_produto, nome_doador, valor_produto, valor_venda FROM produtos";
$count_sql = "SELECT COUNT(*) as total FROM produtos";
$params = [];
if (!empty($search)) {
    $sql .= " WHERE nome_produto LIKE ? OR nome_doador LIKE ?";
    $count_sql .= " WHERE nome_produto LIKE ? OR nome_doador LIKE ?";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $itens_por_pagina;
$params[] = $offset;

// Contar o total de registros para paginação
$stmt = $conn->prepare($count_sql);
if (!empty($search)) {
    $stmt->bind_param("ss", $search_param, $search_param);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $itens_por_pagina);
$stmt->close();

// Executar a consulta principal
$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("ssii", $search_param, $search_param, $itens_por_pagina, $offset);
} else {
    $stmt->bind_param("ii", $itens_por_pagina, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Nome do Produto</th>
                <th>Doador</th>
                <th>Valor Produto</th>
                <th>Valor Venda</th>
                <th>Ações</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $valor_produto = $row['valor_produto'] ? number_format($row['valor_produto'], 2, ',', '.') : '-';
        $valor_venda = $row['valor_venda'] ? number_format($row['valor_venda'], 2, ',', '.') : '-';
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nome_produto']}</td>
                <td>{$row['nome_doador']}</td>
                <td>R$ {$valor_produto}</td>
                <td>R$ {$valor_venda}</td>
                <td>
                    <button class='action-btn edit' onclick='editarProduto({$row['id']})' title='Editar'><i class='fas fa-pencil-alt'></i></button>
                    <button class='action-btn delete' onclick='deletarProduto({$row['id']})' title='Deletar'><i class='fas fa-times'></i></button>
                </td>
              </tr>";
    }
    echo "</table>";

    // Exibir links de paginação
    echo "<div class='pagination'>";
    if ($page > 1) {
        echo "<a href='#' onclick='carregarTabela(" . ($page - 1) . ")'>&laquo; Anterior</a>";
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $page ? " class='active'" : "";
        echo "<a href='#' onclick='carregarTabela($i)'$active>$i</a>";
    }
    if ($page < $total_pages) {
        echo "<a href='#' onclick='carregarTabela(" . ($page + 1) . ")'>Próximo &raquo;</a>";
    }
    echo "</div>";
} else {
    echo "<p>Nenhum produto cadastrado ainda.</p>";
}

$stmt->close();
?>