/**
 * Gas Pump Heaven - Homepage Hero Slider
 * Enqueued on the front page only.
 *
 * Stage 2: arrows + dots. Autoplay still not added — say when you want it.
 *
 * Structure:
 *   #gph-hero-slider-wrapper  -> outer section (CSS ID set in Divi builder)
 *     .gph-arrow-prev / .gph-arrow-next  -> real buttons, already in your HTML
 *     #gph-slider-dots                   -> empty mount, dots generated here
 *     .et_pb_fullwidth_image             -> one per slide (dynamic count)
 */

(() => {
  'use strict';

  const init = () => {
    const wrapper = document.getElementById('gph-hero-slider-wrapper');
    if (!wrapper) return;

    const slides = Array.prototype.slice.call(
      wrapper.querySelectorAll('.et_pb_fullwidth_image')
    );
    if (slides.length < 2) return; // nothing to slide

    const prevBtn = wrapper.querySelector('.gph-arrow-prev');
    const nextBtn = wrapper.querySelector('.gph-arrow-next');
    if (!prevBtn || !nextBtn) return;

    // Dots are optional — if the mount is missing, arrows still work.
    const dotsMount = wrapper.querySelector('#gph-slider-dots');
    const dots = dotsMount
      ? slides.map((_, i) => {
          const dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'gph-dot';
          dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
          dotsMount.appendChild(dot);
          return dot;
        })
      : [];

    let current = 0;

    const goTo = (index) => {
      const next = (index + slides.length) % slides.length;

      slides[current].classList.remove('is-active');
      slides[next].classList.add('is-active');

      if (dots.length) {
        dots[current].classList.remove('is-active');
        dots[next].classList.add('is-active');
      }

      current = next;
    };

    prevBtn.addEventListener('click', () => goTo(current - 1));
    nextBtn.addEventListener('click', () => goTo(current + 1));

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => goTo(i));
    });

    wrapper.classList.add('gph-js-ready'); // turns off the CSS no-JS fallback
    goTo(0); // show slide 1, dot 1
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();