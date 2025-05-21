
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.php">Painel</a></li>
            <li><a href="register.php">Cadastrar Usuário</a></li>
            <li><a href="cadastro_produto.php">Cadastrar Produto</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
    <div class="container admin-container">
        <div class="user-info">
            Usuário logado: <strong><?php echo $_SESSION['user']; ?></strong>
        </div>
        <h1>Painel Administrativo</h1>
        <a href="register.php" class="btn">Adicionar Novo Usuário</a>
        <a href="cadastro_produto.php" class="btn">Cadastrar Produto</a>
        <a href="logout.php" class="btn">Sair</a>
    </div>
</body>
</html>