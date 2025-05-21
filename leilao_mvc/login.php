<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Consulta modificada para funcionar com a estrutura atual do banco
    $stmt = $conn->prepare("SELECT id, password, session_token FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $session_token);
        $stmt->fetch();

        if (password_verify($pass, $hashed_password)) {
            // Gera um token de sessão único
            $session_token = bin2hex(random_bytes(16));
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user_id;
            
            // Verificar se a coluna tipo existe antes de tentar usá-la
            $check_column = $conn->query("SHOW COLUMNS FROM `usuarios` LIKE 'tipo'");
            if ($check_column->num_rows > 0) {
                // Se a coluna existe, buscar o tipo do usuário
                $tipo_stmt = $conn->prepare("SELECT tipo FROM usuarios WHERE id = ?");
                $tipo_stmt->bind_param("i", $user_id);
                $tipo_stmt->execute();
                $tipo_stmt->bind_result($user_type);
                $tipo_stmt->fetch();
                $tipo_stmt->close();
                
                $_SESSION['user_type'] = $user_type ?? 'admin'; // Valor padrão se não existir
            } else {
                // Se a coluna não existe, definir como admin por padrão
                $_SESSION['user_type'] = 'admin';
            }
            
            $_SESSION['session_token'] = $session_token;

            // Atualiza o token de sessão no banco
            $stmt = $conn->prepare("UPDATE usuarios SET session_token = ? WHERE username = ?");
            $stmt->bind_param("ss", $session_token, $user);
            $stmt->execute();

            // Redirecionar com base no tipo de usuário (se existir)
            if (isset($_SESSION['user_type'])) {
                switch ($_SESSION['user_type']) {
                    case 'admin':
                        header("Location: admin.php");
                        break;
                    case 'leiloeiro':
                        header("Location: leilao.php");
                        break;
                    case 'comprador':
                    default:
                        header("Location: lance.php");
                        break;
                }
            } else {
                // Se não tiver tipo definido, redirecionar para admin por padrão
                header("Location: admin.php");
            }
            exit();
        } else {
            $error = "Senha incorreta.";
        }
    } else {
        $error = "Usuário não encontrado.";
    }

    $stmt->close();
}

$page_title = 'Login';
include 'header.php';
?>

<div class="auth-container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <div class="form-group">
            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" placeholder="Digite seu usuário" required>
        </div>
        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <p class="text-center mt-3">Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
</div>

<?php include 'footer.php'; ?>
