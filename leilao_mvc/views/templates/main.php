<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $title ?? 'Sistema de Leilão Beneficente São João Batista';
         ?>
     </title>
<link rel="stylesheet" href="/leilao_mvc/public/css/main.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="/leilao_mvc/">Leilão São João Batista</a>
            </div>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="nav" id="mainNav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/leilao_mvc/" class="nav-link <?php echo $active_page === 'home' ? 'active' : ''; ?>">Início</a>
                    </li>
                    <li class="nav-item">
                        <a href="/leilao_mvc/leilao.php" class="nav-link <?php echo $active_page === 'leilao' ? 'active' : ''; ?>">Leilões</a>
                    </li>
                    <li class="nav-item">
                        <a href="/leilao_mvc/lance.php" class="nav-link <?php echo $active_page === 'lance' ? 'active' : ''; ?>">Lances</a>
                    </li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="/leilao_mvc/admin.php" class="nav-link <?php echo $active_page === 'admin' ? 'active' : ''; ?>">Administração</a>
                    </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="/leilao_mvc/logout.php" class="nav-link">Sair</a>
                    </li>
                    <li class="user-info">
                        Olá, <?php echo htmlspecialchars($_SESSION['user']); ?>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="/leilao_mvc/login.php" class="nav-link <?php echo $active_page === 'login' ? 'active' : ''; ?>">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a href="/leilao_mvc/register.php" class="nav-link <?php echo $active_page === 'register' ? 'active' : ''; ?>">Cadastrar</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php echo $content; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">Sobre Nós</h3>
                    <p>O Sistema de Leilão Beneficente São João Batista foi criado para ajudar a arrecadar fundos para obras de caridade.</p>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Links Rápidos</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="/leilao_mvc/">Início</a></li>
                        <li class="footer-link"><a href="/leilao_mvc/leilao.php">Leilões</a></li>
                        <li class="footer-link"><a href="/leilao_mvc/lance.php">Lances</a></li>
                        <li class="footer-link"><a href="/leilao_mvc/login.php">Entrar</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Contato</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><i class="fas fa-envelope"></i> contato@leilaosaojoaobatista.org</li>
                        <li class="footer-link"><i class="fas fa-phone"></i> (00) 1234-5678</li>
                        <li class="footer-link"><i class="fas fa-map-marker-alt"></i> Rua São João Batista, 123</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Sistema de Leilão Beneficente São João Batista. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Script para o menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const mainNav = document.getElementById('mainNav');
            
            if (menuToggle && mainNav) {
                menuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
