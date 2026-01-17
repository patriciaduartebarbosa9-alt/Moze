document.addEventListener('DOMContentLoaded', () => {
  const profilePicInput = document.getElementById('profile-pic');
  const profilePreview = document.getElementById('profile-preview');
  const editForm = document.querySelector('.edit-form');

  // Carrega imagem salva previamente
  const savedProfilePic = localStorage.getItem('profilePic');
  if (savedProfilePic) {
    const img = document.createElement('img');
    img.src = savedProfilePic;
    img.style.width = '100px';
    img.style.height = '100px';
    img.style.borderRadius = '50%';
    img.style.objectFit = 'cover';
    img.style.marginBottom = '10px';
    img.style.display = 'block';
    profilePreview.innerHTML = '';
    profilePreview.appendChild(img);
  }

  // Quando o utilizador seleciona uma nova imagem
  profilePicInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        const imgData = event.target.result;
        
        // Mostra preview
        profilePreview.innerHTML = '';
        const img = document.createElement('img');
        img.src = imgData;
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.borderRadius = '50%';
        img.style.objectFit = 'cover';
        img.style.marginBottom = '10px';
        img.style.display = 'block';
        profilePreview.appendChild(img);

        // Guarda no localStorage
        localStorage.setItem('profilePic', imgData);

        // Notifica outras abas da mudança
        window.dispatchEvent(new StorageEvent('storage', {
          key: 'profilePic',
          newValue: imgData,
          oldValue: savedProfilePic,
          storageArea: localStorage
        }));

        // Atualiza o header imediatamente na página atual
        const headerImg = document.querySelector('#profile-pic-header');
        if (headerImg) {
          headerImg.src = imgData;
        }
      };
      reader.readAsDataURL(file);
    }
  });

  // Carregar dados do localStorage (portfólio e serviços)
  const savedPortfolio = localStorage.getItem('portfolio');
  if (savedPortfolio) {
    try {
      const portfolioItems = JSON.parse(savedPortfolio);
      const portfolioDiv = document.getElementById('portfolio-items');
      portfolioDiv.innerHTML = '';
      portfolioItems.forEach((item, index) => {
        addPortfolioItemToEdit(item, index);
      });
    } catch (e) {
      console.error('Erro ao carregar portfólio:', e);
    }
  }

  const savedServices = localStorage.getItem('services');
  if (savedServices) {
    const servicesTextarea = document.getElementById('services-text');
    servicesTextarea.value = savedServices;
  }

  // Adicionar item de portfólio
  document.getElementById('add-portfolio-item').addEventListener('click', () => {
    addPortfolioItemToEdit();
  });

  function addPortfolioItemToEdit(item = {}, index = null) {
    const portfolioDiv = document.getElementById('portfolio-items');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'portfolio-item-edit';
    itemDiv.style.marginBottom = '20px';
    itemDiv.style.padding = '15px';
    itemDiv.style.border = '1px solid #ddd';
    itemDiv.style.borderRadius = '8px';

    let preview = '';
    if (item.url) {
      preview = `<img src="${item.url}" class="preview" style="width:100px; height:100px; margin-bottom:10px; border-radius:8px; object-fit:cover; display:block;">`;
    }

    itemDiv.innerHTML = `
      ${preview}
      <input type="hidden" class="portfolio-url" value="${item.url || ''}">
      <input type="file" accept="image/*" class="portfolio-file" style="margin-bottom: 10px;">
      <input type="text" placeholder="Nome" class="portfolio-name" value="${item.name || ''}" style="display: block; width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
      <textarea placeholder="Descrição" class="portfolio-desc" style="display: block; width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: Arial;">${item.desc || ''}</textarea>
      <button type="button" class="remove-item" style="background: #d9534f; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Remover</button>
    `;

    // Preview da imagem ao selecionar
    const fileInput = itemDiv.querySelector('.portfolio-file');
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          itemDiv.querySelector('.portfolio-url').value = event.target.result;
          let previewImg = itemDiv.querySelector('.preview');
          if (!previewImg) {
            previewImg = document.createElement('img');
            previewImg.className = 'preview';
            previewImg.style.width = '100px';
            previewImg.style.height = '100px';
            previewImg.style.borderRadius = '8px';
            previewImg.style.objectFit = 'cover';
            previewImg.style.display = 'block';
            previewImg.style.marginBottom = '10px';
            itemDiv.insertBefore(previewImg, itemDiv.firstChild);
          }
          previewImg.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    // Remover item
    itemDiv.querySelector('.remove-item').addEventListener('click', () => {
      itemDiv.remove();
    });

    portfolioDiv.appendChild(itemDiv);
  }

  // Guardar alterações
  editForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Guardar serviços
    const servicesText = document.getElementById('services-text').value;
    localStorage.setItem('services', servicesText);

    // Guardar portfólio
    const portfolioItems = [];
    document.querySelectorAll('.portfolio-item-edit').forEach(item => {
      portfolioItems.push({
        url: item.querySelector('.portfolio-url').value,
        name: item.querySelector('.portfolio-name').value,
        desc: item.querySelector('.portfolio-desc').value
      });
    });
    localStorage.setItem('portfolio', JSON.stringify(portfolioItems));

    alert('Alterações salvas com sucesso!');
    window.location.href = 'perfil.html';
  });
});
