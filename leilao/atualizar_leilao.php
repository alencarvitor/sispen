<?php
include 'db.php';
include 'session.php';

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'error' => ''];

if ($action == 'atualizar_status') {
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['em_andamento', 'pausado', 'suspenso', 'finalizado'])) {
        $stmt = $conn->prepare("UPDATE leilao SET status = ? WHERE id = 1");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $stmt->close();
        $response['success'] = true;
    } else {
        $response['error'] = 'Status inválido.';
    }
} elseif ($action == 'selecionar_produto') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    if ($produto_id > 0) {
        $stmt = $conn->prepare("SELECT em_leilao FROM produtos WHERE id = ? AND NOT EXISTS (SELECT 1 FROM itens_leiloados WHERE produto_id = ?)");
        $stmt->bind_param("ii", $produto_id, $produto_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result && !$result['em_leilao']) {
            $stmt = $conn->prepare("UPDATE leilao SET produto_id = ?, status = 'em_andamento' WHERE id = 1");
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 1 WHERE id = ?");
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $stmt->close();
            $response['success'] = true;
        } else {
            $response['error'] = 'Produto não disponível para leilão.';
        }
    } else {
        $response['error'] = 'ID do produto inválido.';
    }
} elseif ($action == 'retornar_lista') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    if ($produto_id > 0) {
        $stmt = $conn->prepare("UPDATE leilao SET produto_id = NULL, status = 'pausado' WHERE id = 1");
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 0 WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();
        $response['success'] = true;
    } else {
        $response['error'] = 'ID do produto inválido.';
    }
} elseif ($action == 'marcar_vendido') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    if ($produto_id > 0) {
        $stmt = $conn->prepare("SELECT usuario_id, valor_lance 
                                FROM lances 
                                WHERE produto_id = ? 
                                ORDER BY valor_lance DESC 
                                LIMIT 1");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $lance = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($lance) {
            $stmt = $conn->prepare("INSERT INTO itens_leiloados (produto_id, usuario_id, valor_arremate, data_arremate) 
                                    VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iid", $produto_id, $lance['usuario_id'], $lance['valor_lance']);
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 0 WHERE id = ?");
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("UPDATE leilao SET produto_id = NULL, status = 'pausado' WHERE id = 1");
            $stmt->execute();
            $stmt->close();
            $response['success'] = true;
        } else {
            $response['error'] = 'Nenhum lance encontrado para o produto.';
        }
    } else {
        $response['error'] = 'ID do produto inválido.';
    }
} elseif ($action == 'abrir_lance') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    if ($produto_id > 0) {
        $stmt = $conn->prepare("UPDATE leilao SET status = 'em_andamento', produto_id = ? WHERE id = 1");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 1 WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();
        $response['success'] = true;
    } else {
        $response['error'] = 'ID do produto inválido.';
    }
} elseif ($action == 'retornar_venda') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    if ($item_id > 0) {
        $stmt = $conn->prepare("SELECT produto_id FROM itens_leiloados WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($item) {
            $stmt = $conn->prepare("DELETE FROM itens_leiloados WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 0 WHERE id = ?");
            $stmt->bind_param("i", $item['produto_id']);
            $stmt->execute();
            $stmt->close();
            $response['success'] = true;
        } else {
            $response['error'] = 'Item não encontrado.';
        }
    } else {
        $response['error'] = 'ID do item inválido.';
    }
} elseif ($action == 'buscar_maior_lance') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    if ($produto_id > 0) {
        $stmt = $conn->prepare("SELECT l.valor_lance, u.username 
                                FROM lances l 
                                JOIN usuarios u ON l.usuario_id = u.id 
                                WHERE l.produto_id = ? 
                                ORDER BY l.valor_lance DESC 
                                LIMIT 1");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $lance = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($lance) {
            $response['success'] = true;
            $response['arrematante'] = $lance['username'];
            $response['lance'] = number_format($lance['valor_lance'], 2, ',', '.');
        } else {
            $response['success'] = true;
            $response['arrematante'] = null;
            $response['lance'] = null;
        }
    } else {
        $response['error'] = 'ID do produto inválido.';
    }
} elseif ($action == 'dar_lance') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $valor_lance = (float)($_POST['valor_lance'] ?? 0);
    $usuario_id = $_SESSION['user']['id'];
    if ($produto_id > 0 && $valor_lance > 0) {
        // Verificar se o leilão está em andamento e o produto não foi vendido
        $stmt = $conn->prepare("SELECT status FROM leilao WHERE id = 1 AND produto_id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $leilao = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($leilao && $leilao['status'] == 'em_andamento' && !in_array($produto_id, array_column($conn->query("SELECT produto_id FROM itens_leiloados")->fetch_all(MYSQLI_ASSOC), 'produto_id'))) {
            // Verificar se o lance é maior que o maior lance atual
            $stmt = $conn->prepare("SELECT MAX(valor_lance) as maior_lance FROM lances WHERE produto_id = ?");
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $maior_lance = $stmt->get_result()->fetch_assoc()['maior_lance'] ?? 0;
            $stmt->close();
            if ($valor_lance > $maior_lance) {
                $stmt = $conn->prepare("INSERT INTO lances (produto_id, usuario_id, valor_lance, data_lance) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iid", $produto_id, $usuario_id, $valor_lance);
                $stmt->execute();
                $stmt->close();
                $response['success'] = true;
            } else {
                $response['error'] = 'O lance deve ser maior que o maior lance atual (R$ ' . number_format($maior_lance, 2, ',', '.') . ').';
            }
        } else {
            $response['error'] = 'Leilão não está em andamento ou o produto já foi vendido.';
        }
    } else {
        $response['error'] = 'Dados inválidos.';
    }
} elseif ($action == 'retirar_lance') {
    $lance_id = (int)($_POST['lance_id'] ?? 0);
    $usuario_id = $_SESSION['user']['id'];
    if ($lance_id > 0) {
        // Verificar se o lance pertence ao usuário e se o produto ainda está em leilão
        $stmt = $conn->prepare("SELECT l.produto_id 
                                FROM lances l 
                                JOIN leilao le ON l.produto_id = le.produto_id 
                                WHERE l.id = ? AND l.usuario_id = ? AND le.status = 'em_andamento' 
                                AND NOT EXISTS (SELECT 1 FROM itens_leiloados i WHERE i.produto_id = l.produto_id)");
        $stmt->bind_param("ii", $lance_id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result) {
            $stmt = $conn->prepare("DELETE FROM lances WHERE id = ?");
            $stmt->bind_param("i", $lance_id);
            $stmt->execute();
            $stmt->close();
            $response['success'] = true;
        } else {
            $response['error'] = 'Lance não pode ser retirado: inválido, não pertence a você ou o produto já foi vendido.';
        }
    } else {
        $response['error'] = 'ID do lance inválido.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>