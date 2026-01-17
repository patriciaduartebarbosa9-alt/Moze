<?php
/*
EXEMPLO DE USO DE LOGIN E CERTIFICADO

====================================
1. LOGIN
====================================

ENDPOINT: POST /Moze/api/login.php

DADOS:
{
    "email": "joao@example.com",
    "password": "senha123"
}

RESPOSTA PARA CLIENTE:
{
    "status": "success",
    "message": "Login realizado com sucesso!",
    "data": {
        "id": 1,
        "email": "joao@example.com",
        "nome": "João Silva",
        "tipo": "cliente",
        "foto_perfil": null,
        "token": "abc123def456..."
    }
}

RESPOSTA PARA FOTÓGRAFO SEM CERTIFICADO:
{
    "status": "success",
    "message": "Login realizado com sucesso!",
    "data": {
        "id": 2,
        "email": "maria@example.com",
        "nome": "Maria Santos",
        "tipo": "fotografo",
        "foto_perfil": null,
        "token": "xyz789uvw012...",
        "certificado": {
            "status": "pendente",
            "verificado": false
        },
        "mensagem_certificado": "Por favor, faça upload do seu certificado profissional para completar o perfil"
    }
}

RESPOSTA PARA FOTÓGRAFO COM CERTIFICADO VERIFICADO:
{
    "status": "success",
    "message": "Login realizado com sucesso!",
    "data": {
        "id": 2,
        "email": "maria@example.com",
        "nome": "Maria Santos",
        "tipo": "fotografo",
        "foto_perfil": null,
        "token": "xyz789uvw012...",
        "certificado": {
            "status": "verificado",
            "verificado": true
        }
    }
}

====================================
2. UPLOAD DE CERTIFICADO (FOTÓGRAFOS)
====================================

ENDPOINT: POST /Moze/api/upload_certificado.php

DADOS (multipart/form-data):
- certificado: [ficheiro PDF, JPG ou PNG, máximo 5MB]
- Token/Sessão: Autenticado via SESSION

RESPOSTA:
{
    "status": "success",
    "message": "Certificado enviado com sucesso! Aguardando verificação da administração.",
    "data": {
        "certificado": "cert_2_1705123456.pdf",
        "status": "pendente_verificacao",
        "mensagem": "Seu certificado será verificado em breve"
    }
}

====================================
ERRO DE LOGIN:
====================================

{
    "status": "error",
    "message": "Email ou password incorretos"
}

*/
?>
