let slideIndex = 0;
document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.slide');
  console.log('Slides:', slides);
  const slider = document.querySelector('#slider');
  const totalSlides = slides.length;
  function showSlides(index) {
    if (index >= totalSlides) {
      slideIndex = 0;
    } else if (index < 0) {
      slideIndex = totalSlides - 1;
    } else {
      slideIndex = index;
    }
    const offset = -slideIndex * 100;
    slider.style.transform = 'translateX(' + offset + '%)';
  }
  window.plusSlides = function(n) {
    showSlides(slideIndex + n);
  }
  if (slides.length > 0) {
    showSlides(slideIndex);
  }
});
