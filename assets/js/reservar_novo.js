document.addEventListener('DOMContentLoaded', () => {
  // Mapa de distritos e concelhos
  const concelhosMap = {
    aveiro: ['Águeda', 'Albergaria-a-Velha', 'Anadia', 'Aveiro', 'Estarreja', 'Ílhavo', 'Murtosa', 'Oliveira do Bairro', 'Ovar', 'Sever do Vouga', 'Vagos', 'Espinho', 'Mealhada', 'Santa Maria da Feira', 'São João da Madeira', 'Arouca', 'Vale de Cambra'],
    beja: ['Aljustrel', 'Almodovar', 'Alvito', 'Barrancos', 'Beja', 'Castro Verde', 'Cuba', 'Ferreira do Alentejo', 'Moura', 'Mértola', 'Odemira', 'Serpa', 'Vidigueira'],
    braga: ['Amares', 'Barcelos', 'Braga', 'Cabeceiras de Basto', 'Celorico de Basto', 'Esposende', 'Fafe', 'Guimarães', 'Póvoa de Lanhoso', 'Terras de Bouro', 'Vieira do Minho', 'Vila Nova de Famalicão', 'Vila Verde', 'Vizela'],
    braganca: ['Alfândega da Fé', 'Bragança', 'Carrazeda de Ansiães', 'Freixo de Espada à Cinta', 'Macedo de Cavaleiros', 'Mirando do Douro', 'Mirandela', 'Mogadouro', 'Torre de Moncorvo', 'Vila Flor', 'Vimioso', 'Vinhais'],
    castelobranco: ['Belmonte', 'Castelo Branco', 'Covilhã', 'Fundão', 'Idanha-a-Nova', 'Oleiros', 'Penamacor', 'Proença-a-Nova', 'Sertã', 'Vila de Rei', 'Vila Velha de Ródão'],
    coimbra: ['Arganil', 'Cantanhede', 'Coimbra', 'Condeixa-a-Nova', 'Figueira da Foz', 'Góis', 'Lousã', 'Mealhada', 'Mira', 'Miranda do Corvo', 'Montemor-o-Velho', 'Mortágua', 'Oliveira do Hospital', 'Pampilhose da Serra', 'Penacova', 'Penela', 'Soure', 'Tábua', 'Vila Nova de Poiares'],
    evora: ['Alandroal', 'Arraiolos', 'Borba', 'Évora', 'Estremoz', 'Montemor-o-Novo', 'Mora', 'Mourao', 'Portel', 'Redondo', 'Reguengos de Monsaraz', 'Vendas Novas', 'Viana do Alentejo', 'Vila Viçosa'],
    faro: ['Albufeira', 'Alcoutim', 'Algarve', 'Aljezur', 'Castro Marim', 'Faro', 'Lagoa', 'Lagos', 'Loulé', 'Monchique', 'Olhão', 'Portimão', 'São Brás de Alportel', 'Silves', 'Tavira', 'Vila do Bispo', 'Vila Real de Santo António'],
    guarda: ['Aguiar da Beira', 'Almeida', 'Celorico da Beira', 'Figueira de Castelo Rodrigo', 'Fornos de Algodres', 'Gouveia', 'Guarda', 'Manteigas', 'Mêda', 'Pinhel', 'Sabugal', 'Seia', 'Trancoso', 'Vila Nova de Foz Côa'],
    leiria: ['Alcobaça', 'Alvaiázere', 'Ansião', 'Batalha', 'Bombarral', 'Caldas da Rainha', 'Castanheira de Pera', 'Fátima', 'Leiria', 'Marinha Grande', 'Nazaré', 'Óbidos', 'Pedrógão grande', 'Peniche', 'Pombal', 'Porto de Mós'],
    lisboa: ['Alenquer', 'Amadora', 'Arruda dos Vinhos', 'Azambuja', 'Cadaval', 'Cascais', 'Lisboa', 'Loures', 'Lourinhã', 'Mafra', 'Odivelas', 'Oeiras', 'Sintra', 'Sobral de Monte Agraço', 'Torres Vedras', 'Vila Franca de Xira'],
    portalegre: ['Alter do Chão', 'Arronches', 'Avis', 'Campo Maior', 'Castelo de Vide', 'Crato', 'Elvas', 'Fronteirs', 'Gravião', 'Marvão', 'Monforte', 'Nisa', 'Ponte de Sor', 'Portalegre', 'Ródão', 'Sousel'],
    porto: ['Amarante', 'Baião', 'Felgueiras', 'Gondomar', 'Lousada', 'Maia', 'Marco de Canaveses', 'Matosinhos', 'Paços de Ferreira', 'Paredes', 'Penafiel', 'Porto', 'Póvoa de Varzim', 'Santo Tirso', 'São João da Madeira', 'Trofa', 'Valongo', 'Vila do Conde', 'Vila Nova de Gaia'],
    santarem: ['Abrantes', 'Alcanena', 'Almeirim', 'Alpiarça', 'Benavente', 'Cartaxo', 'Chamusca', 'Constância', 'Coruche', 'Entroncamento', 'Ferreira do Zêzere', 'Golegã', 'Mação', 'Ourém', 'Rio Maior', 'Salvaterra de Magos', 'Santarém', 'Sardoal', 'Tomar', 'Torres Novas', 'Vila Nova da Barquinha'],
    setubal: ['Alcácer do Sal', 'Alcochete', 'Almada', 'Barreiro', 'Grândola', 'Moita', 'Montijo', 'Palmela', 'Santiago do Cacém', 'Seixal', 'Setúbal', 'Sesimbra', 'Sines'],
    vianacastelo: ['Arcos de Valdevez', 'Caminha', 'Melgaço', 'Monção', 'Paredes de Coura', 'Ponte da Barca', 'Ponte de Lima', 'Valença', 'Viana do Castelo', 'Vila Nova de Cerveira'],
    vilareal: ['Alijó', 'Boticas', 'Chaves', 'Mesão Frio', 'Mondim de Basto', 'Montalegre', 'Murça', 'Peso da Régua', 'Ribeira de Pena', 'Sabrosa', 'Santa Marta de Penaguião', 'Valpaços', 'Vila Pouca de Aguiar', 'Vila Real'],
    viseu: ['Armamar', 'Carregal do Sal', 'Castro Daire', 'Cinfães', 'Lamego', 'Mangualde', 'Moimenta da Beira', 'Mortágua', 'Nelas', 'Oliveira de Frades', 'Penalva do Castelo', 'Penedono', 'Resende', 'Santa Comba Dão', 'São João da Pesqueira', 'Sátão', 'Tarouca', 'Tondela', 'Vila Nova de Paiva', 'Viseu', 'Vouzela'],
    acores: ['Angra do Heroísmo', 'Calheta', 'Corvo', 'Horta', 'Lagoa', 'Lajes das Flores', 'Lajes do Pico', 'Madalenia', 'Nordeste', 'Ponta Delgada', 'Praia da Vitória', 'Ribeira Grande', 'Velas', 'Vila do Porto', 'Vila Franca do Campo'],
    madeira: ['Calheta', 'Câmara de Lobos', 'Funchal', 'Machico', 'Ponta do Sol', 'Porto Moniz', 'Porto Santo', 'Santa Cruz', 'Santana', 'São Vicente']
  };

  const distritoSelect = document.getElementById('distrito');
  const concelhoSelect = document.getElementById('concelho');
  const form = document.getElementById('search-form');
  const resultsSection = document.getElementById('results-section');
  const fotografosGrid = document.getElementById('fotografos-grid');

  // Verificar se os elementos existem antes de usar
  if (!distritoSelect || !form) {
    console.warn('Elementos de formulário não encontrados na página');
    return;
  }

  // Evento para atualizar concelhos
  distritoSelect.addEventListener('change', function () {
    const selectedDistrito = this.value;
    
    concelhoSelect.innerHTML = '<option value="">Selecione um concelho</option>';
    concelhoSelect.disabled = true;
    
    if (selectedDistrito && concelhosMap[selectedDistrito]) {
      const concelhos = concelhosMap[selectedDistrito];
      concelhos.forEach(concelho => {
        const option = document.createElement('option');
        option.value = concelho.toLowerCase().replace(/\s+/g, '-');
        option.textContent = concelho;
        concelhoSelect.appendChild(option);
      });
      concelhoSelect.disabled = false;
    }
  });

  // Evento de submit do formulário
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const distrito = distritoSelect.value.trim();
    const concelho = concelhoSelect.value.trim();
    const data = document.getElementById('data').value;
    const hora = document.getElementById('hora').value;
    const categoria = document.getElementById('categoria').value;

    // Validar campos obrigatórios
    if (!distrito) {
      alert('Por favor, selecione um distrito');
      return;
    }

    if (!concelho) {
      alert('Por favor, selecione um concelho');
      return;
    }

    if (!categoria) {
      alert('Por favor, selecione um tipo de fotografia');
      return;
    }

    // Mostrar loading
    fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px; grid-column: 1 / -1;">Carregando fotógrafos...</p>';
    resultsSection.classList.remove('hidden');

    setTimeout(() => {
      resultsSection.scrollIntoView({ behavior: 'smooth' });
    }, 100);

    try {
      // Chamar API
      const url = `/Moze/api/list_fotografos.php?especialidade=${encodeURIComponent(categoria)}`;
      const response = await fetch(url);
      const data_response = await response.json();

      if (data_response.status !== 'success' || data_response.data.total === 0) {
        fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #7b0000; font-weight: bold; font-size: 1rem; grid-column: 1 / -1;">Nenhum fotógrafo encontrado</p>';
        return;
      }

      // Gerar cards dos fotógrafos
      let html = '';
      data_response.data.fotografos.forEach(foto => {
        const imagemUrl = foto.foto_perfil 
          ? `/uploads/perfil/${foto.foto_perfil}` 
          : `https://i.pravatar.cc/150?img=${foto.id}`;
        
        const avaliacaoExibir = foto.avaliacoes.media || '0';
        
        html += `
          <article class="card" data-fotografo-id="${foto.id}">
            <div class="card-header">
              <div>
                <h3>${foto.nome}</h3>
                <p class="location">${foto.bio || 'Fotógrafo profissional'}</p>
              </div>
              <div class="rating">★ ${avaliacaoExibir}</div>
            </div>
            <div class="card-gallery">
              <div class="photo" style="background-image: url('${imagemUrl}');"></div>
              <div class="photo" style="background-image: url('${imagemUrl}');"></div>
              <div class="photo" style="background-image: url('${imagemUrl}');"></div>
              <div class="photo" style="background-image: url('${imagemUrl}');"></div>
            </div>
            <button class="btn-secondary" data-fotografo-id="${foto.id}">Ver perfil</button>
          </article>
        `;
      });

      fotografosGrid.innerHTML = html;

      // Adicionar event listeners
      document.querySelectorAll('.btn-secondary').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const fotografoId = btn.getAttribute('data-fotografo-id');
          const card = btn.closest('.card');
          const nome = card.querySelector('h3').textContent;
          
          // Guardar dados
          const reservaData = {
            fotografo_id: fotografoId,
            nome_fotografo: nome,
            categoria: categoria,
            distrito: distrito,
            concelho: concelho,
            data: data || null,
            hora: hora || null
          };
          
          sessionStorage.setItem('reserva_pendente', JSON.stringify(reservaData));
          alert(`Você selecionou ${nome}\n\nVer mais detalhes em breve.`);
        });
      });

    } catch (error) {
      console.error('Erro:', error);
      fotografosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #7b0000; font-weight: bold; font-size: 1rem; grid-column: 1 / -1;">Erro ao carregar fotógrafos. Tente novamente.</p>';
    }
  });
});
