<!-- View da página inicial -->
<!-- views/home/index.php -->

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Sistema de Leilão Beneficente São João Batista</h1>
            <p class="hero-subtitle">Participe dos nossos leilões e ajude a transformar vidas através da caridade</p>
            <div>
                <a href="/leilao_mvc/leilao.php" class="btn btn-secondary">Ver Leilões Ativos</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/leilao_mvc/login.php" class="btn btn-primary">Entrar para Participar</a>
                <?php endif; ?>
            </div>
            <img src="/leilao_mvc/public/images/sao_joao_batista.jpg" alt="São João Batista" class="hero-image">
        </div>
    </div>
</section>

<section class="home-features">
    <div class="container">
        <h2 class="text-center mb-4">Como Funciona</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="feature-title">Cadastre-se</h3>
                <p>Crie sua conta para participar dos leilões beneficentes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="feature-title">Encontre Itens</h3>
                <p>Navegue pelos itens disponíveis nos leilões ativos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <h3 class="feature-title">Dê seus Lances</h3>
                <p>Participe com seus lances e acompanhe em tempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="feature-title">Ajude a Comunidade</h3>
                <p>Todo valor arrecadado é destinado a obras de caridade.</p>
            </div>
        </div>
    </div>
</section>

<section class="products">
    <div class="container">
        <h2 class="text-center mb-4">Destaques do Leilão</h2>
        <div class="products-grid">
            <!-- Aqui seriam exibidos os produtos em destaque do leilão -->
            <!-- Exemplo estático para demonstração -->
            <div class="product-card">
                <img src="/leilao_mvc/public/images/produto-exemplo.jpg" alt="Produto Exemplo" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Item em Destaque</h3>
                    <p class="product-price">Lance Atual: R$ 150,00</p>
                    <p class="product-donor">Doado por: João da Silva</p>
                    <a href="/leilao_mvc/leilao.php/item/1" class="btn btn-primary mt-2">Ver Detalhes</a>
                </div>
            </div>
            <div class="product-card">
                <img src="/leilao_mvc/public/images/produto-exemplo.jpg" alt="Produto Exemplo" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Item em Destaque</h3>
                    <p class="product-price">Lance Atual: R$ 250,00</p>
                    <p class="product-donor">Doado por: Maria Souza</p>
                    <a href="/leilao_mvc/leilao.php/item/2" class="btn btn-primary mt-2">Ver Detalhes</a>
                </div>
            </div>
            <div class="product-card">
                <img src="/leilao_mvc/public/images/produto-exemplo.jpg" alt="Produto Exemplo" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Item em Destaque</h3>
                    <p class="product-price">Lance Atual: R$ 350,00</p>
                    <p class="product-donor">Doado por: Pedro Oliveira</p>
                    <a href="/leilao_mvc/leilao.php/item/3" class="btn btn-primary mt-2">Ver Detalhes</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Faça a Diferença</h2>
            <p class="cta-text">Participe dos nossos leilões beneficentes e ajude a transformar vidas. Todos os recursos arrecadados são destinados a projetos sociais da comunidade São João Batista.</p>
            <a href="/leilao_mvc/register.php" class="btn btn-secondary">Cadastre-se Agora</a>
        </div>
    </div>
</section>
