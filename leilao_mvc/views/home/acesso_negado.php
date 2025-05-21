<!-- View da página de acesso negado -->
<!-- views/home/acesso_negado.php -->

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2>Acesso Negado</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Você não tem permissão para acessar esta página.
            </div>
            <p>Por favor, entre em contato com o administrador se acredita que isso é um erro.</p>
            <a href="/" class="btn btn-primary">Voltar para a Página Inicial</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/login" class="btn btn-secondary">Fazer Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>
