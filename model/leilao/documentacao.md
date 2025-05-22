# Documentação do Sistema de Leilão

## Visão Geral

Este sistema de leilão foi desenvolvido utilizando PHP, MySQL, JavaScript, CSS e AJAX, conforme solicitado. O sistema permite a realização de leilões online, com funcionalidades específicas para diferentes tipos de usuários (administrador, leiloeiro e comprador).

## Estrutura do Projeto

O projeto segue uma arquitetura MVC simplificada, com a seguinte estrutura de diretórios:

```
leilao/
├── ajax/                  # Arquivos para processamento AJAX
├── assets/                # Recursos estáticos
│   ├── css/               # Arquivos CSS
│   ├── js/                # Arquivos JavaScript
│   └── img/               # Imagens
├── config/                # Arquivos de configuração
├── controllers/           # Controladores (a serem implementados conforme necessidade)
├── models/                # Modelos (a serem implementados conforme necessidade)
├── utils/                 # Classes utilitárias
└── views/                 # Visualizações (a serem implementadas conforme necessidade)
```

## Banco de Dados

O sistema utiliza um banco de dados MySQL com as seguintes tabelas:

1. `tipo_usuario` - Armazena os tipos de usuário (administrador, leiloeiro, comprador)
2. `usuario` - Armazena informações dos usuários
3. `produtos` - Armazena informações dos produtos disponíveis para leilão
4. `lance` - Registra os lances dados pelos usuários
5. `lance_direto` - Registra lances diretos
6. `produto_vendido` - Registra produtos vendidos através de lances
7. `produto_vendido_lance_direto` - Registra produtos vendidos através de lances diretos
8. `sessoes` - Gerencia as sessões dos usuários

## Alterações no Banco de Dados

Foram realizadas as seguintes alterações no esquema original:

1. Adição do campo `IMAGEM` na tabela `produtos`
2. Adição do campo `CPF` na tabela `usuario`
3. Correção do nome da coluna `NOME_USUER` para `NOME_USER` na tabela `usuario`
4. Adição de campos `DATA_HORA` nas tabelas `lance`, `lance_direto`, `produto_vendido` e `produto_vendido_lance_direto`
5. Criação da tabela `sessoes` para gerenciamento de sessões

## Funcionalidades Principais

### 1. Sistema de Autenticação

- Login com CPF e senha
- Cadastro de novos usuários (por padrão, como compradores)
- Recuperação de senha através do CPF
- Gerenciamento de sessões seguras

### 2. Controle de Acesso

- **Administrador**: Acesso total ao sistema
- **Leiloeiro**: Acesso ao painel de lances e gestão de itens
- **Comprador**: Acesso apenas ao painel de lances

### 3. Painel de Lances

- Visualização de produtos disponíveis para leilão
- Sistema de lances com botões de valores (R$ 5, R$ 10, R$ 20, R$ 50, R$ 100, R$ 200)
- Atualização em tempo real dos lances via AJAX
- Histórico de lances para cada produto
- Visualização de itens arrematados pelo usuário

### 4. Gestão de Itens em Leilão

- Visualização de itens em leilão
- Funcionalidade para retirar item do leilão
- Funcionalidade para marcar item como vendido
- Funcionalidade para apagar lance
- Cadastro de novos produtos
- Busca e paginação de produtos

## Arquivos Principais

### Configuração

- `config/config.php` - Configurações globais do sistema
- `config/database.php` - Configuração de conexão com o banco de dados
- `config/alteracoes_db.sql` - Script SQL com alterações no banco de dados

### Utilitários

- `utils/Session.php` - Classe para gerenciamento de sessões
- `utils/Auth.php` - Classe para autenticação de usuários

### Telas Principais

- `index.php` - Página inicial
- `login.php` - Tela de login
- `cadastro.php` - Tela de cadastro de usuário
- `recuperar_senha.php` - Tela de recuperação de senha
- `painel_lances.php` - Painel de lances
- `gestao_itens.php` - Painel de gestão de itens em leilão
- `logout.php` - Script para logout

### AJAX

- `ajax/obter_lances.php` - Obtém lances atualizados de um produto
- `ajax/dar_lance.php` - Processa novos lances
- `ajax/buscar_produtos.php` - Busca produtos
- `ajax/gerenciar_item.php` - Gerencia ações em itens (retirar, vender, apagar lance)

### Layout

- `header.php` - Cabeçalho comum a todas as páginas
- `footer.php` - Rodapé comum a todas as páginas
- `assets/css/style.css` - Estilos CSS
- `assets/js/script.js` - Scripts JavaScript

## Instruções de Instalação

1. **Configuração do Banco de Dados**:
   - Crie um banco de dados MySQL chamado `leilao_db`
   - Execute o script SQL original para criar as tabelas
   - Execute o script `config/alteracoes_db.sql` para aplicar as alterações necessárias

2. **Configuração do Sistema**:
   - Coloque todos os arquivos em um servidor web com suporte a PHP
   - Edite o arquivo `config/database.php` com as credenciais corretas do seu banco de dados
   - Edite o arquivo `config/config.php` e ajuste a constante `BASE_URL` para o URL correto da sua instalação

3. **Acesso ao Sistema**:
   - Acesse o sistema através do navegador
   - Por padrão, três tipos de usuário são criados: administrador, leiloeiro e comprador
   - Cadastre-se como um novo usuário (por padrão, será do tipo comprador)
   - Para criar usuários administradores ou leiloeiros, é necessário alterar diretamente no banco de dados

## Funcionalidades AJAX

O sistema utiliza AJAX para atualização em tempo real, sem a necessidade de recarregar a página. As principais funcionalidades AJAX são:

1. **Atualização de Lances**: Os lances são atualizados automaticamente a cada 5 segundos
2. **Dar Lance**: Os lances são processados sem recarregar a página
3. **Busca de Produtos**: A busca de produtos é realizada sem recarregar a página
4. **Gerenciamento de Itens**: As ações de gerenciamento (retirar, vender, apagar lance) são processadas via AJAX

## Esquema de Cores

O sistema utiliza o seguinte esquema de cores, conforme solicitado:

- `#F2F1DC` - Cor de fundo
- `#F2BB13` - Cor terciária
- `#F29C50` - Cor secundária
- `#401201` - Cor escura
- `#F26430` - Cor primária

## Considerações Finais

Este sistema foi desenvolvido para atender às necessidades específicas de um leilão temporário, com foco na simplicidade e eficiência. O sistema não utiliza WebSockets, conforme solicitado, mas implementa atualizações em tempo real através de AJAX.

Para qualquer dúvida ou suporte adicional, entre em contato com o desenvolvedor.
