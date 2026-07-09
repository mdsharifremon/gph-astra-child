/**
 * Gas Pump Heaven - Homepage Hero Slider
 * Enqueued on the front page only.
 *
 * Stage 3: arrows + dots + autoplay.
 */

(() => {
  'use strict';

  const AUTOPLAY_MS = 5000;

  const init = () => {
    const wrapper = document.getElementById('gph-hero-slider-wrapper');
    if (!wrapper) return;

    const slides = Array.prototype.slice.call(
      wrapper.querySelectorAll('.et_pb_fullwidth_image')
    );
    if (slides.length < 2) return;

    const prevBtn = wrapper.querySelector('.gph-arrow-prev');
    const nextBtn = wrapper.querySelector('.gph-arrow-next');
    if (!prevBtn || !nextBtn) return;

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
    let timer = null;

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

    const goNext = () => goTo(current + 1);
    const goPrev = () => goTo(current - 1);

    const play = () => {
      stop();
      timer = setInterval(goNext, AUTOPLAY_MS);
    };
    const stop = () => {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    };

    prevBtn.addEventListener('click', () => { goPrev(); play(); });
    nextBtn.addEventListener('click', () => { goNext(); play(); });
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); play(); });
    });

    wrapper.addEventListener('mouseenter', stop);
    wrapper.addEventListener('mouseleave', play);
    wrapper.addEventListener('focusin', stop);
    wrapper.addEventListener('focusout', play);

    wrapper.classList.add('gph-js-ready');
    goTo(0);
    play();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();