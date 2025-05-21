    </div><!-- /.container -->
    
    <footer>
        <div class="container">
            <p class="text-center">&copy; <?php echo date('Y'); ?> Sistema de Leilão para Caridade</p>
        </div>
    </footer>

    <script>
    // Função para mostrar notificações
    function mostrarNotificacao(mensagem, tipo = 'success') {
        const notificacao = document.createElement('div');
        notificacao.className = `notificacao ${tipo}`;
        notificacao.textContent = mensagem;
        
        document.body.appendChild(notificacao);
        
        // Mostrar com animação
        setTimeout(() => {
            notificacao.classList.add('show');
        }, 10);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notificacao.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notificacao);
            }, 300);
        }, 3000);
    }
    </script>
</body>
</html>
