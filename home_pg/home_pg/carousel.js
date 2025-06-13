const carousel = document.querySelector('.carousel');
const carouselItems = document.querySelectorAll('.carousel-item');
const prevBtn = document.querySelector('.carousel-prev');
const nextBtn = document.querySelector('.carousel-next');

let currentItem = 0;
let isTransitioning = false; // Flag to prevent multiple transitions

// Function to slide to the next item
const nextSlide = () => {
  if (isTransitioning) return; // Prevent multiple transitions
  isTransitioning = true; // Set flag to true

  currentItem = (currentItem + 1) % carouselItems.length;
  const translateX = -currentItem * 100 + '%'; // Adjust based on item width

  carousel.style.transition = 'transform 0.5s ease-in-out'; // Add transition
  carousel.style.transform = `translateX(${translateX})`;

  // After the transition is complete
  setTimeout(() => {
    isTransitioning = false; // Reset flag
    carousel.style.transition = ''; // Reset transition
  }, 500);
};

// Function to slide to the previous item
const prevSlide = () => {
  if (isTransitioning) return; // Prevent multiple transitions
  isTransitioning = true; // Set flag to true

  currentItem = (currentItem - 1 + carouselItems.length) % carouselItems.length; // This will make the index loop back to the beginning
  const translateX = -currentItem * 100 + '%'; // Adjust based on item width

  carousel.style.transition = 'transform 0.5s ease-in-out'; // Add transition
  carousel.style.transform = `translateX(${translateX})`;

  // After the transition is complete
  setTimeout(() => {
    isTransitioning = false; // Reset flag
    carousel.style.transition = ''; // Reset transition
  }, 500);
};

// Event listeners for the buttons
prevBtn.addEventListener('click', (e) => {
  e.stopPropagation(); // Prevent carousel item click
  prevSlide();
});

nextBtn.addEventListener('click', (e) => {
  e.stopPropagation(); // Prevent carousel item click
  nextSlide();
});

// Prevent button clicks from triggering carousel item clicks
document.querySelectorAll('.carousel-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
  });
});

// Auto slide every 6 seconds (increased for better UX)
let autoSlideInterval = setInterval(nextSlide, 6000);

// Pause auto-slide on hover
const carouselContainer = document.querySelector('.carousel-container');
carouselContainer.addEventListener('mouseenter', () => {
  clearInterval(autoSlideInterval);
});

carouselContainer.addEventListener('mouseleave', () => {
  autoSlideInterval = setInterval(nextSlide, 6000);
});

// Add keyboard navigation
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowLeft') {
    prevSlide();
  } else if (e.key === 'ArrowRight') {
    nextSlide();
  }
});