-- ============================================
-- Script de Migração de Utilizadores Existentes
-- ============================================

-- 1. Migrar Clientes
-- Cria registos na tabela clientes para todos os utilizadores do tipo 'cliente' que ainda não têm registo
INSERT INTO clientes (utilizador_id, localizacao)
SELECT id, 'Não especificado' 
FROM utilizadores 
WHERE tipo = 'cliente' 
AND id NOT IN (SELECT utilizador_id FROM clientes);

-- 2. Migrar Fotógrafos
-- Cria registos na tabela fotografos para todos os utilizadores do tipo 'fotografo' que ainda não têm registo
INSERT INTO fotografos (utilizador_id, especialidades, preco_hora, disponivel, certificado_verificado)
SELECT id, 'Não especificado', 0, 1, 0
FROM utilizadores 
WHERE tipo = 'fotografo' 
AND id NOT IN (SELECT utilizador_id FROM fotografos);

-- 3. Verificar Migração
-- Ver quantos clientes foram criados
SELECT COUNT(*) as total_clientes FROM clientes;

-- Ver quantos fotógrafos foram criados
SELECT COUNT(*) as total_fotografos FROM fotografos;

-- Ver se todos os utilizadores foram migrados
SELECT 
    tipo,
    COUNT(*) as total_utilizadores,
    (SELECT COUNT(DISTINCT utilizador_id) FROM clientes WHERE clientes.utilizador_id = utilizadores.id) as migrados
FROM utilizadores
GROUP BY tipo;
