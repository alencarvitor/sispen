<?php
session_start();
include 'db.php';
include 'permissions.php';

// Verificar permissões (admin pode cadastrar qualquer tipo, outros só comprador)
$is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'comprador';
    
    // Se não for admin, só pode cadastrar comprador
    if (!$is_admin) {
        $tipo = 'comprador';
    }
    
    // Verificar se as senhas coincidem
    if ($password !== $confirm_password) {
        $error = "As senhas não coincidem.";
    } else {
        // Verificar se o usuário já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Este nome de usuário já está em uso.";
        } else {
            // Hash da senha
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Verificar se a coluna tipo existe
            $check_column = $conn->query("SHOW COLUMNS FROM `usuarios` LIKE 'tipo'");
            
            if ($check_column->num_rows > 0) {
                // Se a coluna existe, incluir na inserção
                $stmt = $conn->prepare("INSERT INTO usuarios (username, password, tipo) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $tipo);
            } else {
                // Se a coluna não existe, não incluir na inserção
                $stmt = $conn->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $hashed_password);
            }
            
            if ($stmt->execute()) {
                $success = "Usuário cadastrado com sucesso!";
            } else {
                $error = "Erro ao cadastrar usuário: " . $conn->error;
            }
        }
        
        $stmt->close();
    }
}

$page_title = 'Cadastro de Usuário';
include 'header.php';
?>

<div class="auth-container">
    <h2>Cadastro de Usuário</h2>
    <?php if (isset($success)) echo "<p class='alert alert-success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>
    
    <form method="post">
        <div class="form-group">
            <label for="username">Nome de Usuário</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmar Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <?php 
        // Verificar se a coluna tipo existe antes de mostrar o select
        $check_column = $conn->query("SHOW COLUMNS FROM `usuarios` LIKE 'tipo'");
        if ($check_column->num_rows > 0 && $is_admin): 
        ?>
        <div class="form-group">
            <label for="tipo">Tipo de Usuário</label>
            <select id="tipo" name="tipo">
                <option value="comprador">Comprador</option>
                <option value="leiloeiro">Leiloeiro</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <?php endif; ?>
        
        <button type="submit" class="btn">Cadastrar</button>
    </form>
    
    <p class="text-center mt-3">Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
</div>

<?php include 'footer.php'; ?>
