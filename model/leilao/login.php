<?php
/**
 * Página de login do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Verificar se o usuário já está logado
if ($loggedIn) {
    redirect('index.php');
}

// Processar formulário de login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = sanitize($_POST['cpf'] ?? '');
    $senha = sanitize($_POST['senha'] ?? '');
    
    if (empty($cpf) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $result = $auth->login($cpf, $senha);
        
        if ($result) {
            redirect('index.php');
        } else {
            $error = 'CPF ou senha incorretos.';
        }
    }
}
?>

<div class="row">
    <div class="col-6" style="margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Login</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" id="login-form">
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required>
                    </div>
                    
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
                    <p><a href="recuperar_senha.php">Esqueceu sua senha?</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'footer.php';
?>
