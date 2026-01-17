document.addEventListener('DOMContentLoaded', () => {
  // Lista de cidades
  const cidades = [
    'Porto',
    'Lisboa',
    'Covilhã',
    'Braga',
    'Guarda',
    'Aveiro',
    'Coimbra',
    'Leiria',
    'Castelo Branco',
    'Faro',
    'Setúbal',
    'Évora'
  ];

  // Autocomplete
  const localidadeInput = document.getElementById('localidade');
  const autocompleteList = document.getElementById('autocomplete-list');

  function mostrarSugestoes(valor) {
    valor = valor.toLowerCase().trim();

    let filtradas;
    if (valor.length === 0) {
      filtradas = cidades;
    } else {
      filtradas = cidades.filter(cidade => 
        cidade.toLowerCase().startsWith(valor)
      );
    }

    if (filtradas.length === 0) {
      autocompleteList.style.display = 'none';
      document.querySelector('.autocomplete-container').classList.remove('active');
      return;
    }

    // Mostrar sugestões
    autocompleteList.innerHTML = filtradas.map(cidade => {
      if (valor.length === 0) {
        return `<li data-cidade="${cidade}">${cidade}</li>`;
      }
      
      const indice = cidade.toLowerCase().indexOf(valor);
      const antes = cidade.substring(0, indice);
      const destaque = cidade.substring(indice, indice + valor.length);
      const depois = cidade.substring(indice + valor.length);
      
      return `<li data-cidade="${cidade}">${antes}<strong>${destaque}</strong>${depois}</li>`;
    }).join('');

    autocompleteList.style.display = 'block';
    document.querySelector('.autocomplete-container').classList.add('active');

    // Adicionar event listeners aos itens
    document.querySelectorAll('.autocomplete-list li').forEach(li => {
      li.addEventListener('click', () => {
        localidadeInput.value = li.getAttribute('data-cidade');
        autocompleteList.style.display = 'none';
        document.querySelector('.autocomplete-container').classList.remove('active');
      });
    });
  }

  // Mostrar sugestões ao focar no input
  localidadeInput.addEventListener('focus', () => {
    mostrarSugestoes(localidadeInput.value);
  });

  // Filtrar sugestões enquanto escreve
  localidadeInput.addEventListener('input', (e) => {
    mostrarSugestoes(e.target.value);
  });

  // Fechar autocomplete ao clicar fora
  document.addEventListener('click', (e) => {
    if (e.target !== localidadeInput && !e.target.closest('.autocomplete-list')) {
      autocompleteList.style.display = 'none';
      document.querySelector('.autocomplete-container').classList.remove('active');
    }
  });

  // Fechar autocomplete ao pressionar Escape
  localidadeInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      autocompleteList.style.display = 'none';
      document.querySelector('.autocomplete-container').classList.remove('active');
    }
  });

  // Ver preços
  const filterBtn = document.querySelector('.filter-btn');
  const fotografosContainer = document.getElementById('fotografos-container');
  const fotografosGrid = document.getElementById('fotografos-grid');
  
  const fotoTipo = document.getElementById('fotografia-tipo');
  const dataDisponivel = document.getElementById('data-disponivel');
  const horaDisponivel = document.getElementById('hora-disponivel');

  filterBtn.addEventListener('click', async () => {
    const tipo = fotoTipo.value;
    const localidade = localidadeInput.value;
    
    // Validações: apenas local e tipo são obrigatórios
    if (!localidade) {
      alert('Por favor, selecione um local de destino');
      return;
    }
    
    if (!tipo) {
      alert('Por favor, selecione um tipo de fotografia');
      return;
    }
    
    // Data e hora são opcionais, mas se preenchidas serão guardadas
    
    // Mostrar loading
    fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px;">Carregando fotógrafos...</p>';
    fotografosContainer.style.display = 'block';
    
    // Scroll suave até a seção de fotografos
    setTimeout(() => {
      fotografosContainer.scrollIntoView({ behavior: 'smooth' });
    }, 100);
    
    try {
      // Chamar API
      const url = `/Moze/api/list_fotografos.php?especialidade=${encodeURIComponent(tipo)}`;
      const response = await fetch(url);
      const data = await response.json();
      
      if (data.status !== 'success' || data.data.total === 0) {
        fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #7b0000; font-weight: bold; font-size: 16px;">Nenhum fotógrafo encontrado</p>';
        return;
      }
      
      // Gerar cards dos fotógrafos
      let html = '';
      data.data.fotografos.forEach(foto => {
        const imagemUrl = foto.foto_perfil 
          ? `/uploads/perfil/${foto.foto_perfil}` 
          : `https://i.pravatar.cc/150?img=${foto.id}`;
        
        const especialidadesText = foto.especialidades.join(', ');
        const avaliacaoStar = '★'.repeat(Math.round(foto.avaliacoes.media));
        
        html += `
          <div class="fotografo-card" data-fotografo-id="${foto.id}">
            <div class="fotografo-header">
              <img src="${imagemUrl}" alt="${foto.nome}" class="fotografo-avatar">
              <div class="fotografo-info">
                <h3>${foto.nome}</h3>
                <p class="especialidade">${especialidadesText}</p>
                <div class="rating">
                  <span class="stars">${avaliacaoStar}</span>
                  <span class="reviews">(${foto.avaliacoes.total} avaliações)</span>
                </div>
              </div>
            </div>

            <div class="fotografo-details">
              <p class="bio">${foto.bio_profissional || foto.bio || 'Fotógrafo profissional'}</p>
              
              <div class="preco-info">
                <span class="preco-label">A partir de:</span>
                <span class="preco-valor">€${foto.preco_hora}/hora</span>
              </div>

              <div class="disponibilidades">
                <p class="label">Próximas disponibilidades:</p>
                <div class="slots">
                  ${foto.proximas_datas.map(data => `<span class="slot">${data}</span>`).join('')}
                </div>
              </div>
            </div>

            <button class="reservar-btn">Reservar</button>
          </div>
        `;
      });
      
      fotografosGrid.innerHTML = html;
      
      // Adicionar event listeners aos botões de reserva
      document.querySelectorAll('.reservar-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const card = e.target.closest('.fotografo-card');
          const fotografoId = card.getAttribute('data-fotografo-id');
          const nome = card.querySelector('.fotografo-info h3').textContent;
          const especialidade = card.querySelector('.especialidade').textContent;
          
          // Guardar dados da reserva (apenas os preenchidos)
          const reservaData = {
            fotografo_id: fotografoId,
            nome_fotografo: nome,
            especialidade: especialidade,
            tipo_fotografia: tipo,
            localidade: localidade,
            data: dataDisponivel.value || null,
            hora: horaDisponivel.value || null
          };
          
          // Guardar em sessionStorage para usar na próxima página
          sessionStorage.setItem('reserva_pendente', JSON.stringify(reservaData));
          
          alert(`Você selecionou ${nome}\n\nEspecialidade: ${especialidade}\n\nProceda para confirmar a reserva.`);
        });
      });
      
    } catch (error) {
      console.error('Erro:', error);
      fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #7b0000; font-weight: bold; font-size: 16px;">Erro ao carregar fotógrafos. Tente novamente.</p>';
    }
  });
});
