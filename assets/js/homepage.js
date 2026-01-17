document.addEventListener('DOMContentLoaded', () => {
  // Toggle entre Sou Cliente e Sou Fotógrafo
  const toggleButtons = document.querySelectorAll('.toggle-btn');
  const userTypeSections = document.querySelectorAll('.user-type-section');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Remove active de todos os botões
      toggleButtons.forEach(btn => btn.classList.remove('active'));
      // Adiciona ao clicado
      button.classList.add('active');
      
      const userType = button.dataset.type;
      
      // Esconde todas as secções
      userTypeSections.forEach(section => {
        section.classList.remove('active');
      });
      
      // Mostra apenas a secção do tipo selecionado
      const activeSection = document.querySelector(`.user-type-section[data-type="${userType}"]`);
      if (activeSection) {
        activeSection.classList.add('active');
      }
    });
  });

  // Formulário de contacto
  const contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      const formData = new FormData(contactForm);
      console.log('Form submitted:', Object.fromEntries(formData));
      
      // Aqui você pode enviar os dados para um servidor
      alert('Mensagem enviada com sucesso!');
      contactForm.reset();
    });
  }
});
