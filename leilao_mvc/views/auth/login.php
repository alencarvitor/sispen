<!-- View da página de login -->
<!-- views/auth/login.php -->

<div class="container mt-5">
    <div class="auth-container">
        <h2 class="text-center mb-4">Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="/login">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Digite seu usuário" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Digite sua senha" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p>Não tem uma conta? <a href="/register">Cadastre-se aqui</a></p>
        </div>
    </div>
</div>
