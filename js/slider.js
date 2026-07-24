/**
 * AIML AcademicHub - Department Management Portal
 * Hero Image Slider Script
 */

document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dots .dot');
    let currentIndex = 0;
    const slideInterval = 5000; // 5 seconds interval
    let autoSlideTimer;

    if (slides.length === 0) return;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) dots[i].classList.remove('active');
        });

        slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        currentIndex = index;
    }

    function nextSlide() {
        let nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
    }

    // Dot click event listeners
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', function() {
            showSlide(idx);
            resetAutoSlide();
        });
    });

    function startAutoSlide() {
        autoSlideTimer = setInterval(nextSlide, slideInterval);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideTimer);
        startAutoSlide();
    }

    // Initialize
    showSlide(0);
    startAutoSlide();
});
