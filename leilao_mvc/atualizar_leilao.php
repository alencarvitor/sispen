<?php
session_start();
include 'db.php';

// Definir cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false];

// Ação para verificar se produto está ativo
if (isset($_POST['action']) && $_POST['action'] === 'verificar_produto_ativo') {
    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Produto não especificado']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    
    // Verificar se o produto está em leilão ativo
    $stmt = $conn->prepare("SELECT status FROM leilao WHERE id = 1 AND produto_id = ?");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($result && $result['status'] === 'em_andamento') {
        echo json_encode(['success' => true, 'ativo' => true]);
    } else {
        echo json_encode(['success' => true, 'ativo' => false]);
    }
    exit;
}

// Ação para dar lance
if (isset($_POST['action']) && $_POST['action'] === 'dar_lance') {
    if (!isset($_POST['produto_id']) || !isset($_POST['valor_lance'])) {
        echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    $valor_lance = (float)$_POST['valor_lance'];

    // Verificar se o produto está em leilão - MOVIDO PARA O INÍCIO
    $stmt = $conn->prepare("SELECT status, produto_id FROM leilao WHERE id = 1");
    $stmt->execute();
    $leilao = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($leilao['status'] !== 'em_andamento' || $leilao['produto_id'] != $produto_id) {
        echo json_encode(['success' => false, 'error' => 'Este produto não está em leilão ativo']);
        exit;
    }

    // Verificar se o valor do lance é maior que o lance atual
    $stmt = $conn->prepare("SELECT MAX(valor_lance) as maior_lance FROM lances WHERE produto_id = ?");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $maior_lance = $result['maior_lance'] ?? 0;
    $stmt->close();

    if ($valor_lance <= $maior_lance) {
        echo json_encode(['success' => false, 'error' => 'O valor do lance deve ser maior que o lance atual']);
        exit;
    }

    // Registrar o lance
    $stmt = $conn->prepare("INSERT INTO lances (produto_id, usuario_id, valor_lance, data_lance) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iid", $produto_id, $user_id, $valor_lance);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Lance registrado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao registrar lance: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Ação para buscar maior lance
if (isset($_GET['action']) && $_GET['action'] === 'buscar_maior_lance') {
    if (!isset($_GET['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Produto não especificado']);
        exit;
    }

    $produto_id = (int)$_GET['produto_id'];

    // Buscar maior lance e arrematante
    $stmt = $conn->prepare("SELECT l.valor_lance, u.username 
                            FROM lances l 
                            JOIN usuarios u ON l.usuario_id = u.id 
                            WHERE l.produto_id = ? 
                            ORDER BY l.valor_lance DESC 
                            LIMIT 1");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result) {
        $response = [
            'success' => true,
            'arrematante' => $result['username'],
            'lance' => number_format($result['valor_lance'], 2, ',', '.')
        ];
    } else {
        $response = [
            'success' => true,
            'arrematante' => null,
            'lance' => null
        ];
    }

    echo json_encode($response);
    exit;
}

// Ação para atualizar status do leilão (apenas admin ou leiloeiro)
if (isset($_POST['action']) && $_POST['action'] === 'atualizar_status') {
    // Verificar permissões
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'leiloeiro'])) {
        echo json_encode(['success' => false, 'error' => 'Permissão negada']);
        exit;
    }

    if (!isset($_POST['status'])) {
        echo json_encode(['success' => false, 'error' => 'Status não especificado']);
        exit;
    }

    $status = $_POST['status'];
    $status_validos = ['em_andamento', 'pausado', 'suspenso', 'finalizado'];
    
    if (!in_array($status, $status_validos)) {
        echo json_encode(['success' => false, 'error' => 'Status inválido']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE leilao SET status = ? WHERE id = 1");
    $stmt->bind_param("s", $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar status: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Ação para selecionar produto para leilão (apenas admin ou leiloeiro)
if (isset($_POST['action']) && $_POST['action'] === 'selecionar_produto') {
    // Verificar permissões
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'leiloeiro'])) {
        echo json_encode(['success' => false, 'error' => 'Permissão negada']);
        exit;
    }

    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Produto não especificado']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];

    // Verificar se o produto existe e não está em leilão
    $stmt = $conn->prepare("SELECT id FROM produtos WHERE id = ? AND em_leilao = 0");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Produto não encontrado ou já está em leilão']);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Atualizar produto para em_leilao = 1
        $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 1 WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();

        // Atualizar leilão para usar este produto
        $stmt = $conn->prepare("UPDATE leilao SET produto_id = ? WHERE id = 1");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Produto selecionado para leilão com sucesso']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Erro ao selecionar produto: ' . $e->getMessage()]);
    }
    exit;
}

// Ação para marcar produto como vendido (apenas admin ou leiloeiro)
if (isset($_POST['action']) && $_POST['action'] === 'marcar_vendido') {
    // Verificar permissões
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'leiloeiro'])) {
        echo json_encode(['success' => false, 'error' => 'Permissão negada']);
        exit;
    }

    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Produto não especificado']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];

    // Buscar maior lance e arrematante
    $stmt = $conn->prepare("SELECT l.valor_lance, l.usuario_id 
                            FROM lances l 
                            WHERE l.produto_id = ? 
                            ORDER BY l.valor_lance DESC 
                            LIMIT 1");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Não há lances para este produto']);
        exit;
    }

    $valor_arremate = $result['valor_lance'];
    $usuario_id = $result['usuario_id'];

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Inserir em itens_leiloados
        $stmt = $conn->prepare("INSERT INTO itens_leiloados (produto_id, usuario_id, valor_arremate, data_arremate) 
                                VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iid", $produto_id, $usuario_id, $valor_arremate);
        $stmt->execute();
        $stmt->close();

        // Atualizar produto para em_leilao = 0
        $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 0 WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();

        // Atualizar leilão para produto_id = NULL
        $stmt = $conn->prepare("UPDATE leilao SET produto_id = NULL WHERE id = 1");
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Produto marcado como vendido com sucesso']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Erro ao marcar produto como vendido: ' . $e->getMessage()]);
    }
    exit;
}

// Ação para retornar produto para lista (apenas admin ou leiloeiro)
if (isset($_POST['action']) && ($_POST['action'] === 'retornar_para_lista' || $_POST['action'] === 'retornar_lista')) {
    // Verificar permissões
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'leiloeiro'])) {
        echo json_encode(['success' => false, 'error' => 'Permissão negada']);
        exit;
    }

    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Produto não especificado']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Atualizar produto para em_leilao = 0
        $stmt = $conn->prepare("UPDATE produtos SET em_leilao = 0 WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->close();

        // Atualizar leilão para produto_id = NULL
        $stmt = $conn->prepare("UPDATE leilao SET produto_id = NULL WHERE id = 1");
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Produto retornado para lista com sucesso']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Erro ao retornar produto para lista: ' . $e->getMessage()]);
    }
    exit;
}

// Ação para retornar item para venda (apenas admin ou leiloeiro)
if (isset($_POST['action']) && ($_POST['action'] === 'retornar_para_venda' || $_POST['action'] === 'retornar_venda')) {
    // Verificar permissões
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'leiloeiro'])) {
        echo json_encode(['success' => false, 'error' => 'Permissão negada']);
        exit;
    }

    if (!isset($_POST['item_id'])) {
        echo json_encode(['success' => false, 'error' => 'Item não especificado']);
        exit;
    }

    $item_id = (int)$_POST['item_id'];

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Buscar informações do item leiloado
        $stmt = $conn->prepare("SELECT produto_id FROM itens_leiloados WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$result) {
            throw new Exception("Item não encontrado");
        }
        
        $produto_id = $result['produto_id'];
        
        // Remover da tabela itens_leiloados
        $stmt = $conn->prepare("DELETE FROM itens_leiloados WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Item retornado para venda com sucesso']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Erro ao retornar item para venda: ' . $e->getMessage()]);
    }
    exit;
}

// Ação para notificar atualização (usado para sincronização entre usuários)
if (isset($_POST['action']) && $_POST['action'] === 'notificar_atualizacao') {
    // Aqui poderia ser implementado um mecanismo mais sofisticado de notificação
    // como websockets ou um sistema de polling com timestamp
    // Por enquanto, apenas retornamos sucesso
    echo json_encode(['success' => true, 'message' => 'Notificação registrada']);
    exit;
}

// Se chegou aqui, a ação não foi reconhecida
echo json_encode(['success' => false, 'error' => 'Ação não reconhecida']);
