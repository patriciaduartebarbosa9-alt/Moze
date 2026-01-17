document.addEventListener('DOMContentLoaded', () => {
  // Blog filtering
  const filterTags = document.querySelectorAll('.filter-tag');
  const blogCards = document.querySelectorAll('.blog-card');
  const searchInput = document.getElementById('blog-search');
  const noResults = document.querySelector('.no-results');

  // Filter by category
  filterTags.forEach(tag => {
    tag.addEventListener('click', () => {
      // Remove active class from all tags
      filterTags.forEach(t => t.classList.remove('active'));
      // Add active to clicked tag
      tag.classList.add('active');

      const category = tag.dataset.category;
      filterBlogCards(category, searchInput.value);
    });
  });

  // Search functionality
  searchInput.addEventListener('input', () => {
    const activeCategory = document.querySelector('.filter-tag.active').dataset.category;
    filterBlogCards(activeCategory, searchInput.value);
  });

  // Search button
  const searchBtn = document.querySelector('.search-btn');
  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      const activeCategory = document.querySelector('.filter-tag.active').dataset.category;
      filterBlogCards(activeCategory, searchInput.value);
    });
  }

  // Filter function
  function filterBlogCards(category, searchTerm) {
    let visibleCount = 0;

    blogCards.forEach(card => {
      const cardCategory = card.dataset.category;
      const title = card.querySelector('h3').textContent.toLowerCase();
      const excerpt = card.querySelector('.blog-excerpt').textContent.toLowerCase();
      const searchLower = searchTerm.toLowerCase();

      // Check category
      const categoryMatch = category === 'todos' || cardCategory === category;

      // Check search term
      const searchMatch = searchTerm === '' || title.includes(searchLower) || excerpt.includes(searchLower);

      if (categoryMatch && searchMatch) {
        card.classList.remove('hidden');
        visibleCount++;
      } else {
        card.classList.add('hidden');
      }
    });

    // Show/hide no results message
    if (visibleCount === 0) {
      noResults.style.display = 'block';
    } else {
      noResults.style.display = 'none';
    }
  }

  // Newsletter form
  const newsletterForm = document.querySelector('.newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = newsletterForm.querySelector('input[type="email"]').value;
      console.log('Newsletter subscription:', email);
      alert('Obrigado por se inscrever!');
      newsletterForm.reset();
    });
  }
});
