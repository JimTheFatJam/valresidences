document.addEventListener('DOMContentLoaded', () => {
    initializeSlider();
})
function initializeSlider() {
    const sliderContainer = document.querySelector('.slideshow-container');
    const slider = sliderContainer.querySelector('.slider');
    let slides = [...slider.querySelectorAll('.slide')];
    const nextBtn = sliderContainer.querySelector('.arrows .next')
    const prevBtn = sliderContainer.querySelector('.arrows .prev')
    const dotsContainer = sliderContainer.querySelector('.dots');

    //slider variables
    // let currentIndex = 0;
    let currentIndex = 1;
    let isAnimating = false;
    let interval;
    //clone first and last slides for smooth looping
    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[slides.length - 1].cloneNode(true);
    slider.appendChild(firstClone);
    slider.insertBefore(lastClone, slides[0]);

    // set initial position to slider after inserting clones
    slider.style.transform = `translateX(-${currentIndex * 100}%)`;
    //update slides list
    slides = [...slider.querySelectorAll('.slide')];
    //insert dots
    slides.forEach((slide, index) => {
        //ignore cloned nodes
        if (index === 0 || index + 1 === slides.length) {
            return;
        }
        //way 1.
        // set first dot as active
        // currentIndex == index 
        // ?  dotsContainer.innerHTML += `<span class="dot active"></span>`
        // : dotsContainer.innerHTML += `<span class="dot"></span>`;

        //way 2.
        let dot = document.createElement('span')
        dot.classList.add('dot');
        index == currentIndex && dot.classList.add('active');
        dotsContainer.appendChild(dot);
    });
    // select all dots in js
    const allDots = [...dotsContainer.querySelectorAll('.dot')];

    // create a reusable function for updateSlide on every interaction
    function updateSlide() {
        slider.style.transition = "transform 0.5s ease"
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;

        //update dots
        // allDots.forEach((dot, index) => {
        //     if (index == currentIndex) {
        //         dot.classList.add('active')
        //     } else {
        //         dot.classList.remove('active')
        //     }
        // });

        //update dots (with looping effect)
        allDots.forEach((dot, index) => { //nutrilize all the dots
            dot.classList.remove('active');
        })

        //correcting the active dot index (since currentIndex includes clones)
        if (currentIndex === 0) {
            allDots[allDots.length - 1].classList.add("active");
        } else if (currentIndex === slides.length - 1) {
            allDots[0].classList.add("active");
        } else {
            allDots[currentIndex - 1].classList.add("active");
        }

        //Handle infinit looping effect here
        setTimeout(() => {
            if (currentIndex >= slides.length - 1) {
                slider.style.transition = "none";
                currentIndex = 1;
                slider.style.transform = `translateX(-100%)`;
                dots[0].classList.add('active');
            } else if (currentIndex <= 0) {
                slider.style.transition = "none";
                currentIndex = slides.length - 2;
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;
                dots[dots.length - 1].classList.add('active');
            }
        }, 500)
    }

    function nextSlide() { // for next button click
        if (isAnimating) return;
        isAnimating = true;
        // currentIndex = (currentIndex + 1) % slides.length
        currentIndex++;
        updateSlide()
        setTimeout(() => {
            isAnimating = false;
        }, 500)
        resetAutoSlide()
    }
    function prevSlide() {// for prev button click
        if (isAnimating) return;
        isAnimating = true;
        // currentIndex = (currentIndex - 1 + slides.length) % slides.length
        currentIndex--;
        updateSlide()
        setTimeout(() => {
            isAnimating = false;
        }, 500)
        resetAutoSlide()
    }
    function goToSlide(index) {// for dots
        if (isAnimating) return;
        isAnimating = true;
        // currentIndex = index;
        currentIndex = index + 1;
        updateSlide()
        setTimeout(() => {
            isAnimating = false;
        }, 500)
        resetAutoSlide()
    }
    // reset autoslide on interaction
    function resetAutoSlide() {
        clearInterval(interval);
        //re-initialize interval
        interval = setInterval(nextSlide, 3000);
    }
    //add auto slide functionality
    interval = setInterval(nextSlide, 3000);


    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);
    allDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goToSlide(index)
        })
    })
}
