document.addEventListener("DOMContentLoaded", () => {
  // Tabs functionality
  const buttons = document.querySelectorAll(".tab-btn");
  const panels = document.querySelectorAll(".tab-panel");

  buttons.forEach(button => {
    button.addEventListener("click", () => {
      buttons.forEach(btn => btn.classList.remove("active"));
      panels.forEach(panel => panel.classList.remove("active"));

      button.classList.add("active");

      const target = button.dataset.tab;
      document.getElementById(target).classList.add("active");
    });
  });

  // Portfolio modal functionality
  const portfolioImages = document.querySelectorAll(".portfolio img");
  const modal = document.createElement("div");
  modal.className = "modal";
  modal.innerHTML = `
    <div class="modal-content">
      <span class="close">&times;</span>
      <img class="modal-img" src="" alt="">
    </div>
  `;
  document.body.appendChild(modal);

  const modalImg = modal.querySelector(".modal-img");
  const closeBtn = modal.querySelector(".close");

  portfolioImages.forEach(img => {
    img.addEventListener("click", () => {
      modal.style.display = "block";
      modalImg.src = img.src;
    });
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });

  // Load services from localStorage
  const savedServices = localStorage.getItem('services');
  if (savedServices) {
    const servicesList = document.querySelector('.services');
    servicesList.innerHTML = '';
    const servicesArray = savedServices.split('\n').filter(s => s.trim());
    servicesArray.forEach(service => {
      const li = document.createElement('li');
      li.textContent = service;
      servicesList.appendChild(li);
    });
  }

  // Load profile pic from localStorage
  const savedProfilePic = localStorage.getItem('profilePic');
  if (savedProfilePic) {
    // Atualiza avatar principal do perfil
    const avatar = document.querySelector('.avatar');
    if (avatar) {
      avatar.src = savedProfilePic;
      avatar.style.width = '64px';
      avatar.style.height = '64px';
      avatar.style.objectFit = 'cover';
      avatar.style.borderRadius = '50%';
    }
    // Atualiza avatar do header
    const headerAvatar = document.querySelector('#profile-pic-header');
    if (headerAvatar) {
      headerAvatar.src = savedProfilePic;
    }
  }

  // Load portfolio from localStorage
  const savedPortfolio = localStorage.getItem('portfolio');
  if (savedPortfolio) {
    const portfolioItems = JSON.parse(savedPortfolio);
    const portfolioDiv = document.querySelector('.portfolio');
    portfolioDiv.innerHTML = ''; // Clear defaults
    portfolioItems.forEach(item => {
      const div = document.createElement('div');
      div.className = 'portfolio-item';
      div.innerHTML = `
        <img src="${item.url}" alt="${item.name}">
        <h4>${item.name}</h4>
        <p>${item.desc}</p>
      `;
      div.querySelector('img').addEventListener('click', () => {
        modal.style.display = 'block';
        modalImg.src = item.url;
      });
      portfolioDiv.appendChild(div);
    });
  }

  // Edit functionality
  const editButtons = document.querySelectorAll(".edit-btn");

  editButtons.forEach(button => {
    button.addEventListener("click", () => {
      const editType = button.dataset.edit;
      if (editType === "profile") {
        editProfile();
      } else if (editType === "portfolio") {
        editPortfolio();
      } else if (editType === "servicos") {
        editServicos();
      }
    });
  });

  function editProfile() {
    const name = prompt("Novo nome:", document.querySelector(".profile-info h2").textContent);
    if (name) document.querySelector(".profile-info h2").textContent = name;

    const desc = prompt("Nova descrição:", document.querySelector(".profile-info p").textContent);
    if (desc) document.querySelector(".profile-info p").textContent = desc;

    const avatar = prompt("Novo URL da foto de perfil:", document.querySelector(".avatar").src);
    if (avatar) document.querySelector(".avatar").src = avatar;
  }

  function editPortfolio() {
    const newImg = prompt("Adicionar nova imagem (URL):");
    if (newImg) {
      const img = document.createElement("img");
      img.src = newImg;
      img.addEventListener("click", () => {
        modal.style.display = "block";
        modalImg.src = img.src;
      });
      document.querySelector(".portfolio").appendChild(img);
    }
  }

  function editServicos() {
    const servicesList = document.querySelector(".services");
    const newService = prompt("Adicionar novo serviço:");
    if (newService) {
      const li = document.createElement("li");
      li.textContent = newService;
      servicesList.appendChild(li);
    }
  }

  // Password form
  const passwordForm = document.querySelector(".password-form");
  if (passwordForm) {
    passwordForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Palavra passe alterada com sucesso! (Simulação)");
      // In a real app, send to server
    });
  }
});