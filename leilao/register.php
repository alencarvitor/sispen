<?php
include 'session.php';
include 'db.php';

// Restringir acesso a usuários autenticados
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $email = trim($_POST['email']);

    // Validações
    $errors = [];
    if (empty($username)) {
        $errors[] = "O nome de usuário é obrigatório.";
    }
    if (empty($password)) {
        $errors[] = "A senha é obrigatória.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O e-mail informado é inválido.";
    }

    // Verificar se o username ou email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? OR (email = ? AND email != '')");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "O nome de usuário ou e-mail já está em uso.";
    }
    $stmt->close();

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (username, password, telefone, endereco, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password_hash, $telefone, $endereco, $email);

        if ($stmt->execute()) {
            $success = "Usuário cadastrado com sucesso! <a href='login.php'>Ir para login</a>";
        } else {
            $error = "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
     <link rel="stylesheet" href="css/register.css?v=<?php echo time(); ?>">
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
    <div class="container">
        <h2>Cadastro de Usuário</h2>
        <?php
        if (isset($error)) echo "<p class='error'>$error</p>";
        if (isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="post">
            <input type="text" name="username" placeholder="Usuário OU Apelido" required>
            <input type="password" name="password" placeholder="Senha" required>
            <input type="text" name="telefone" placeholder="Telefone">
            <input type="text" name="endereco" placeholder="Endereço">
            <input type="email" name="email" placeholder="E-mail">
            <input type="submit" value="Cadastrar">
        </form>
        <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>