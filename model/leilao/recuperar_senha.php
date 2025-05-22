<?php
/**
 * Página de recuperação de senha do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Verificar se o usuário já está logado
if ($loggedIn) {
    redirect('index.php');
}

// Processar formulário de recuperação de senha
$error = '';
$success = '';
$mostrarFormSenha = false;
$cpfVerificado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificação de CPF
    if (isset($_POST['verificar_cpf'])) {
        $cpf = sanitize($_POST['cpf'] ?? '');
        
        if (empty($cpf)) {
            $error = 'Por favor, informe seu CPF.';
        } else {
            // Verificar se o CPF existe
            if ($auth->cpfExiste($cpf)) {
                $mostrarFormSenha = true;
                $cpfVerificado = $cpf;
            } else {
                $error = 'CPF não encontrado no sistema.';
            }
        }
    }
    
    // Alteração de senha
    if (isset($_POST['alterar_senha'])) {
        $cpf = sanitize($_POST['cpf'] ?? '');
        $nova_senha = sanitize($_POST['nova_senha'] ?? '');
        $confirmar_senha = sanitize($_POST['confirmar_senha'] ?? '');
        
        if (empty($cpf) || empty($nova_senha) || empty($confirmar_senha)) {
            $error = 'Por favor, preencha todos os campos.';
            $mostrarFormSenha = true;
            $cpfVerificado = $cpf;
        } elseif ($nova_senha !== $confirmar_senha) {
            $error = 'As senhas não coincidem.';
            $mostrarFormSenha = true;
            $cpfVerificado = $cpf;
        } else {
            // Atualizar senha
            $result = $auth->recuperarSenha($cpf, $nova_senha);
            
            if ($result) {
                $success = 'Senha alterada com sucesso! Você já pode fazer login com a nova senha.';
            } else {
                $error = 'Erro ao alterar senha. Por favor, tente novamente.';
                $mostrarFormSenha = true;
                $cpfVerificado = $cpf;
            }
        }
    }
}
?>

<div class="row">
    <div class="col-6" style="margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Recuperação de Senha</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-primary">Ir para Login</a>
                    </div>
                <?php elseif ($mostrarFormSenha): ?>
                    <form method="POST" action="recuperar_senha.php" id="form-nova-senha">
                        <input type="hidden" name="cpf" value="<?php echo $cpfVerificado; ?>">
                        
                        <div class="form-group">
                            <label for="nova_senha">Nova Senha:</label>
                            <input type="password" id="nova_senha" name="nova_senha" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Nova Senha:</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" name="alterar_senha" class="btn btn-primary">Alterar Senha</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Para recuperar sua senha, informe seu CPF cadastrado no sistema:</p>
                    
                    <form method="POST" action="recuperar_senha.php" id="form-verificar-cpf">
                        <div class="form-group">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" required>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" name="verificar_cpf" class="btn btn-primary">Verificar</button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <p><a href="login.php">Voltar para Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'footer.php';
?>
