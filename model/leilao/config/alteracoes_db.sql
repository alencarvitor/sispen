-- Script SQL para alterações nas tabelas do banco de dados
-- Sistema de Leilão

-- Adicionar campo de imagem na tabela produtos
ALTER TABLE produtos ADD COLUMN IMAGEM VARCHAR(255) AFTER NOME_DOADOR;

-- Adicionar campo de CPF na tabela usuario
ALTER TABLE usuario ADD COLUMN CPF VARCHAR(14) AFTER SOBRENOME_USER;

-- Corrigir nome da coluna NOME_USUER para NOME_USER na tabela usuario
ALTER TABLE usuario CHANGE COLUMN NOME_USUER NOME_USER VARCHAR(250);

-- Adicionar campos de data e hora nas tabelas de lance e lance_direto
ALTER TABLE lance ADD COLUMN DATA_HORA DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE lance_direto ADD COLUMN DATA_HORA DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Adicionar campos de data e hora nas tabelas produto_vendido e produto_vendido_lance_direto
ALTER TABLE produto_vendido ADD COLUMN DATA_HORA DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE produto_vendido_lance_direto ADD COLUMN DATA_HORA DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Criar tabela para gerenciamento de sessões
CREATE TABLE sessoes (
    ID_SESSAO INT AUTO_INCREMENT,
    PK_USER INT,
    TOKEN VARCHAR(255),
    DATA_INICIO DATETIME DEFAULT CURRENT_TIMESTAMP,
    DATA_EXPIRACAO DATETIME,
    IP VARCHAR(45),
    USER_AGENT VARCHAR(255),
    ATIVA TINYINT(1) DEFAULT 1,
    PRIMARY KEY (ID_SESSAO),
    FOREIGN KEY (PK_USER) REFERENCES usuario(ID_USER)
);

-- Inserir tipos de usuário padrão
INSERT INTO tipo_usuario (TIPO, DESCRICAO) VALUES 
('administrador', 'Acesso total ao sistema'),
('leiloeiro', 'Gerencia itens e leilões'),
('comprador', 'Participa com lances nos leilões');
