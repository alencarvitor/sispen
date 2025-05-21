<?php
session_start();
include 'db.php';
include 'permissions.php';

// Verificar permissões (admin ou leiloeiro)
verificarPermissao(['admin', 'leiloeiro']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = $_POST['nome_produto'];
    $nome_doador = $_POST['nome_doador'];
    $descricao = $_POST['descricao'];
    
    // Verificar se foi enviado um arquivo
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem = $_FILES['imagem']['name'];
        $temp = $_FILES['imagem']['tmp_name'];
        
        // Verificar se o diretório Uploads existe, se não, criar
        if (!file_exists('Uploads')) {
            mkdir('Uploads', 0777, true);
        }
        
        // Mover o arquivo para o diretório Uploads
        move_uploaded_file($temp, "Uploads/" . $imagem);
    } else {
        $imagem = null;
    }
    
    // Inserir produto no banco de dados
    $stmt = $conn->prepare("INSERT INTO produtos (nome_produto, nome_doador, descricao, imagem) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome_produto, $nome_doador, $descricao, $imagem);
    
    if ($stmt->execute()) {
        $success = "Produto cadastrado com sucesso!";
    } else {
        $error = "Erro ao cadastrar produto: " . $conn->error;
    }
    
    $stmt->close();
}

$page_title = 'Cadastro de Produto';
include 'header.php';
?>

<div class="card">
    <div class="card-header">
        <h1>Cadastro de Produto</h1>
    </div>
    <div class="card-body">
        <?php if (isset($success)) echo "<p class='alert alert-success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome_produto">Nome do Produto</label>
                <input type="text" id="nome_produto" name="nome_produto" required>
            </div>
            
            <div class="form-group">
                <label for="nome_doador">Nome do Doador</label>
                <input type="text" id="nome_doador" name="nome_doador" required>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagem">Imagem</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
            </div>
            
            <button type="submit" class="btn">Cadastrar Produto</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
