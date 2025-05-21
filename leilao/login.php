<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($pass, $hashed_password)) {
            // Gera um token de sessão único
            $session_token = bin2hex(random_bytes(16));
            $_SESSION['user'] = $user;
            $_SESSION['session_token'] = $session_token;

            // Atualiza o token de sessão no banco, invalidando sessões anteriores
            $stmt = $conn->prepare("UPDATE usuarios SET session_token = ? WHERE username = ?");
            $stmt->bind_param("ss", $session_token, $user);
            $stmt->execute();

            header("Location: admin.php");
            exit();
        } else {
            $error = "Senha incorreta.";
        }
    } else {
        $error = "Usuário não encontrado.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="container">
<h2>Login</h2>
<?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<input type="text" name="username" placeholder="Usuário" required><br>
<input type="password" name="password" placeholder="Senha" required><br>
<input type="submit" value="Entrar">
</form>
<p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
</div>
</body>
</html>

