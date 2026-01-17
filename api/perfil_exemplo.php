<?php
/*
EXEMPLO DE USO - VER E EDITAR PERFIL

====================================
1. VER PERFIL
====================================

ENDPOINT: GET /Moze/api/get_perfil.php

AUTENTICAÇÃO: Session (precisa de estar logado)

RESPOSTA PARA CLIENTE:
{
    "status": "success",
    "message": "Perfil carregado com sucesso",
    "data": {
        "id": 1,
        "email": "joao@example.com",
        "nome": "João Silva",
        "telefone": "912345678",
        "tipo": "cliente",
        "foto_perfil": null,
        "bio": null,
        "data_criacao": "2025-01-17 10:30:45",
        "cliente": {
            "localizacao": "Porto",
            "preferencias": null
        }
    }
}

RESPOSTA PARA FOTÓGRAFO:
{
    "status": "success",
    "message": "Perfil carregado com sucesso",
    "data": {
        "id": 2,
        "email": "maria@example.com",
        "nome": "Maria Santos",
        "telefone": "987654321",
        "tipo": "fotografo",
        "foto_perfil": null,
        "bio": "Fotógrafa profissional",
        "data_criacao": "2025-01-17 11:00:00",
        "fotografo": {
            "especialidades": ["retrato", "casamento", "evento"],
            "preco_hora": 50.00,
            "avaliacoes_media": 4.8,
            "numero_avaliacoes": 12,
            "bio_profissional": "5 anos de experiência",
            "portfolio_url": "https://...",
            "disponivel": true,
            "certificado": {
                "ficheiro": "cert_2_1705123456.pdf",
                "verificado": true
            }
        }
    }
}

====================================
2. ATUALIZAR PERFIL
====================================

ENDPOINT: POST /Moze/api/update_perfil.php

AUTENTICAÇÃO: Session (precisa de estar logado)

DADOS PARA CLIENTE:
{
    "nome": "João Silva Santos",
    "telefone": "912345678",
    "bio": "Amante de fotografia",
    "localizacao": "Lisboa"
}

DADOS PARA FOTÓGRAFO:
{
    "nome": "Maria Santos",
    "telefone": "987654321",
    "bio": "Fotógrafa criativa",
    "especialidades": "retrato,casamento,evento,produto",
    "preco_hora": 60.00,
    "bio_profissional": "Especialista em eventos corporativos",
    "portfolio_url": "https://mariasantos.com",
    "disponivel": true
}

UPLOAD DE FOTO DE PERFIL:
Content-Type: multipart/form-data

Campos:
- foto_perfil: [ficheiro JPG/PNG/GIF, máximo 2MB]
- Mais os dados acima (opcional)

RESPOSTA:
{
    "status": "success",
    "message": "Perfil atualizado com sucesso!",
    "data": {
        "id": 1,
        "tipo": "cliente"
    }
}

====================================
EXEMPLO COM JAVASCRIPT
====================================

// Ver Perfil
fetch('/Moze/api/get_perfil.php', {
    method: 'GET',
    credentials: 'include'
})
.then(res => res.json())
.then(data => console.log(data.data));

// Editar Perfil (dados)
fetch('/Moze/api/update_perfil.php', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        nome: "Novo Nome",
        telefone: "912345678"
    })
})
.then(res => res.json())
.then(data => console.log(data));

// Editar Perfil (com foto)
const formData = new FormData();
formData.append('foto_perfil', fileInput.files[0]);
formData.append('nome', 'Novo Nome');

fetch('/Moze/api/update_perfil.php', {
    method: 'POST',
    credentials: 'include',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));

*/
?>
