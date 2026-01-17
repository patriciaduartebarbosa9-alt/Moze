document.addEventListener('DOMContentLoaded', () => {
  // Atualizar avatar do header com imagem do localStorage
  function updateHeaderAvatar() {
    const savedProfilePic = localStorage.getItem('profilePic');
    const profilePicHeader = document.querySelector('#profile-pic-header');
    
    if (profilePicHeader && savedProfilePic) {
      profilePicHeader.src = savedProfilePic;
    }
  }

  // Marcar o link de navegação ativo baseado na página atual
  function setActiveNavLink() {
    const pathname = window.location.pathname;
    const segments = pathname.split('/').filter(s => s);
    const currentPage = segments[segments.length - 1] || 'index.html';
    const fullPath = pathname.toLowerCase();
    
    const navLinks = document.querySelectorAll('.nav a[data-page]');
    
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      const dataPage = link.getAttribute('data-page');
      
      // Remove a classe active de todos primeiro
      link.classList.remove('active');
      
      // Verifica se o href ou data-page corresponde à página atual
      let isActive = false;
      
      if (dataPage) {
        // Verificar blog primeiro (tem prioridade absoluta)
        if (dataPage === 'blog' && fullPath.includes('/blog/')) {
          isActive = true;
        } else if (dataPage === 'index' && !fullPath.includes('/blog/') && (currentPage === 'index.html' || currentPage === '' || currentPage === 'Moze')) {
          isActive = true;
        } else if (dataPage === 'about' && currentPage === 'homepage.html' && !fullPath.includes('/blog/')) {
          isActive = true;
        } else if (!fullPath.includes('/blog/') && currentPage.includes(dataPage) && dataPage !== 'blog' && dataPage !== 'index' && dataPage !== 'about') {
          isActive = true;
        }
      }
      
      if (isActive) {
        link.classList.add('active');
      }
    });
  }

  // Atualiza ao carregar
  updateHeaderAvatar();
  setActiveNavLink();

  // Atualiza avatar sempre que o localStorage mudar (em outra aba ou após edição)
  window.addEventListener('storage', function(e) {
    if (e.key === 'profilePic') updateHeaderAvatar();
  });
});