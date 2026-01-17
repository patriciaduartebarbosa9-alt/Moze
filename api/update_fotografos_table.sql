-- Script para adicionar campos de certificado à tabela fotografos
-- Execute isto no phpMyAdmin se ainda não tiver estes campos

ALTER TABLE fotografos ADD COLUMN certificado VARCHAR(255) AFTER disponivel;
ALTER TABLE fotografos ADD COLUMN certificado_verificado BOOLEAN DEFAULT FALSE AFTER certificado;
