<!-- View da página de registro -->
<!-- views/auth/register.php -->

<div class="container mt-5">
    <div class="auth-container">
        <h2 class="text-center mb-4">Cadastro de Usuário</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="/register">
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Digite seu nome de usuário" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Digite sua senha" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirme sua senha" required>
            </div>
            
            <?php if (isset($is_admin) && $is_admin): ?>
            <div class="form-group">
                <label for="tipo">Tipo de Usuário</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="comprador">Comprador</option>
                    <option value="leiloeiro">Leiloeiro</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p>Já tem uma conta? <a href="/login">Faça login aqui</a></p>
        </div>
    </div>
</div>
