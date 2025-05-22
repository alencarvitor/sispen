<?php
/**
 * Página de cadastro de usuário do Sistema de Leilão
 */

// Incluir cabeçalho
include_once 'header.php';

// Verificar se o usuário já está logado
if ($loggedIn) {
    redirect('index.php');
}

// Processar formulário de cadastro
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome'] ?? '');
    $sobrenome = sanitize($_POST['sobrenome'] ?? '');
    $cpf = sanitize($_POST['cpf'] ?? '');
    $telefone = sanitize($_POST['telefone'] ?? '');
    $endereco = sanitize($_POST['endereco'] ?? '');
    $senha = sanitize($_POST['senha'] ?? '');
    $confirmar_senha = sanitize($_POST['confirmar_senha'] ?? '');
    
    if (empty($nome) || empty($sobrenome) || empty($cpf) || empty($telefone) || empty($endereco) || empty($senha) || empty($confirmar_senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif ($senha !== $confirmar_senha) {
        $error = 'As senhas não coincidem.';
    } else {
        // Verificar se o CPF já está cadastrado
        if ($auth->cpfExiste($cpf)) {
            $error = 'Este CPF já está cadastrado.';
        } else {
            // Dados do usuário para registro
            $user_data = [
                'nome' => $nome,
                'sobrenome' => $sobrenome,
                'cpf' => $cpf,
                'telefone' => $telefone,
                'endereco' => $endereco,
                'senha' => $senha
            ];
            
            $result = $auth->register($user_data);
            
            if ($result) {
                $success = 'Cadastro realizado com sucesso! Você já pode fazer login.';
            } else {
                $error = 'Erro ao realizar cadastro. Por favor, tente novamente.';
            }
        }
    }
}
?>

<div class="row">
    <div class="col-6" style="margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Cadastro de Usuário</h2>
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
                <?php else: ?>
                    <form method="POST" action="cadastro.php" id="cadastro-form" onsubmit="return validateForm('cadastro-form')">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="nome">Nome:</label>
                                    <input type="text" id="nome" name="nome" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="sobrenome">Sobrenome:</label>
                                    <input type="text" id="sobrenome" name="sobrenome" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone">Telefone:</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="endereco">Endereço:</label>
                            <input type="text" id="endereco" name="endereco" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="senha">Senha:</label>
                                    <input type="password" id="senha" name="senha" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="confirmar_senha">Confirmar Senha:</label>
                                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include_once 'footer.php';
?>
