<?php
/*
EXEMPLO DE USO DO REGISTO

Este ficheiro mostra como registar um novo utilizador (cliente ou fotografo)

ENDPOINTS:
POST /Moze/api/register.php

DADOS PARA CLIENTE:
{
    "nome": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "tipo": "cliente",
    "telefone": "912345678",
    "localizacao": "Porto"
}

DADOS PARA FOTÓGRAFO:
{
    "nome": "Maria Santos",
    "email": "maria@example.com",
    "password": "senha123",
    "tipo": "fotografo",
    "telefone": "987654321",
    "especialidades": "retrato,casamento,evento",
    "preco_hora": 50.00,
    "bio": "Fotógrafa profissional com 5 anos de experiência"
}

RESPOSTA DE SUCESSO:
{
    "status": "success",
    "message": "Registo realizado com sucesso!",
    "data": {
        "id": 1,
        "email": "joao@example.com",
        "nome": "João Silva",
        "tipo": "cliente"
    }
}

RESPOSTA DE ERRO:
{
    "status": "error",
    "message": "Email já registado"
}

*/
?>
