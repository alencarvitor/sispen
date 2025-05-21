<?php
// Arquivo para redirecionar usuários sem permissão
session_start();
include 'header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Acesso Negado</h2>
    </div>
    <div class="card-body">
        <p>Você não tem permissão para acessar esta página.</p>
        <p>Por favor, entre em contato com o administrador se acredita que isso é um erro.</p>
        <a href="login.php" class="btn">Voltar para Login</a>
    </div>
</div>

<?php include 'footer.php'; ?>
