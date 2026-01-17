<?php
/*
EXEMPLO DE USO - LISTAR FOTÓGRAFOS DISPONÍVEIS

====================================
ENDPOINT: GET /Moze/api/list_fotografos.php
====================================

PARÂMETROS (opcionais):
- especialidade: filtra por especialidade (retrato, casamento, evento, etc)
- preco_max: filtra por preço máximo por hora
- ordem: ordena por 'avaliacoes', 'preco', 'nome' (padrão: avaliacoes)

====================================
EXEMPLOS DE CHAMADAS
====================================

1. Listar todos os fotógrafos:
   GET /Moze/api/list_fotografos.php

2. Listar fotógrafos de retrato:
   GET /Moze/api/list_fotografos.php?especialidade=retrato

3. Listar fotógrafos com preço até €50/hora:
   GET /Moze/api/list_fotografos.php?preco_max=50

4. Filtro combinado (casamento, até €100, ordenado por preço):
   GET /Moze/api/list_fotografos.php?especialidade=casamento&preco_max=100&ordem=preco

====================================
RESPOSTA
====================================

{
    "status": "success",
    "message": "Fotógrafos carregados com sucesso",
    "data": {
        "total": 3,
        "fotografos": [
            {
                "id": 1,
                "utilizador_id": 2,
                "nome": "Maria Santos",
                "foto_perfil": "perfil_2_1705123456.jpg",
                "bio": "Fotógrafa criativa",
                "especialidades": ["retrato", "casamento", "evento"],
                "preco_hora": 50.00,
                "avaliacoes": {
                    "media": 4.8,
                    "total": 12
                },
                "bio_profissional": "5 anos de experiência em fotografia profissional",
                "portfolio_url": "https://mariasantos.com",
                "disponivel": true,
                "certificado_verificado": true,
                "proximas_datas": [
                    "2025-01-18",
                    "2025-01-19",
                    "2025-01-20"
                ]
            },
            {
                "id": 2,
                "utilizador_id": 3,
                "nome": "João Silva",
                "foto_perfil": "perfil_3_1705123500.jpg",
                "bio": "Especialista em eventos",
                "especialidades": ["evento", "produto"],
                "preco_hora": 45.00,
                "avaliacoes": {
                    "media": 4.5,
                    "total": 8
                },
                "bio_profissional": "Documentação de eventos corporativos",
                "portfolio_url": "https://joaosilva.pt",
                "disponivel": true,
                "certificado_verificado": true,
                "proximas_datas": [
                    "2025-01-18",
                    "2025-01-21"
                ]
            }
        ],
        "filtros": {
            "especialidade": "evento",
            "preco_maximo": "100",
            "ordenado_por": "avaliacoes"
        }
    }
}

====================================
EXEMPLO COM JAVASCRIPT
====================================

// Buscar todos os fotógrafos
fetch('/Moze/api/list_fotografos.php')
    .then(res => res.json())
    .then(data => {
        console.log('Total:', data.data.total);
        data.data.fotografos.forEach(foto => {
            console.log(foto.nome, '-', foto.avaliacoes.media, '⭐');
        });
    });

// Filtrar por especialidade
fetch('/Moze/api/list_fotografos.php?especialidade=casamento&preco_max=100')
    .then(res => res.json())
    .then(data => console.log(data));

// Ordenado por preço (mais barato)
fetch('/Moze/api/list_fotografos.php?ordem=preco')
    .then(res => res.json())
    .then(data => console.log(data));

*/
?>
