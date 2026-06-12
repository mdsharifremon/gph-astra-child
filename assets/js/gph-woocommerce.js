/**
 * Gas Pump Heaven - WooCommerce Catalog JS
 * Feature: Per-image streaming skeleton loader for product & category cards
 *
 * Behavior:
 * - Attach per-image load/error handlers (immediate reveal for cached images)
 * - Observe DOM for images added dynamically (MutationObserver)
 * - Minimal fallback (per-image timeout only as last resort)
 *
 * Usage: Enqueue on catalog pages only (you're already doing that).
 */

(() => {
  'use strict';

  /* ========================================================================
     FEATURE: SKELETON LOADER - START
     ======================================================================== */

  // Updated IMAGE_SELECTOR to explicitly include product image wrappers
  const IMAGE_SELECTOR = [
    '.woocommerce ul.products li.product .astra-shop-thumbnail-wrap img',
    '.woocommerce ul.products li.product .et_shop_image img',
    '.woocommerce ul.products li.product img',
    '.woocommerce ul.products li.product-category img'
  ].join(', ');

  const CARD_SELECTOR = 'li.product, li.product-category';
  const FALLBACK_TIMEOUT_MS = 100000; // long fallback so we avoid batch reveals; acts as last-resort only

  // WeakMap to store per-image fallback timers so we can clear them when image loads
  const fallbackTimers = new WeakMap();

  /**
   * markLoaded - add class to the card containing the image
   * @param {HTMLImageElement} img
   */
  const markLoaded = (img) => {
    try {
      const li = img.closest(CARD_SELECTOR);
      if (!li) return;
      if (!li.classList.contains('gph-img-loaded')) {
        li.classList.add('gph-img-loaded');
      }
      // clear any fallback timer
      const timer = fallbackTimers.get(img);
      if (timer) {
        clearTimeout(timer);
        fallbackTimers.delete(img);
      }
    } catch {
      // swallow errors; we don't want UI crash
    }
  };

  /**
   * handleImage - attach events and fallback for a single image element
   * @param {HTMLImageElement} img
   */
  const handleImage = (img) => {
    if (!img || img._gphHandled) return; 
    img._gphHandled = true;

    // If already loaded (cached), reveal immediately
    if (img.complete && img.naturalWidth > 0) {
      markLoaded(img);
      return;
    }

    // Normal path: reveal on load or error (error still stops shimmer)
    const onLoad = () => markLoaded(img);
    img.addEventListener('load', onLoad, { once: true });
    img.addEventListener('error', onLoad, { once: true });

    // Fallback: if an image never fires load/error (very rare), reveal it after long timeout
    const timer = setTimeout(() => {
      markLoaded(img);
    }, FALLBACK_TIMEOUT_MS);
    fallbackTimers.set(img, timer);
  };

  /**
   * initSkeletonLoader - scan existing images and attach handlers
   */
  const initSkeletonLoader = () => {
    const images = document.querySelectorAll(IMAGE_SELECTOR);
    if (!images || images.length === 0) return;
    images.forEach(handleImage);
  };

  /**
   * observeNewImages - watch for dynamically added images (e.g., lazy loaders or JS templates)
   */
  const observeNewImages = () => {
    const container = document.querySelector('.woocommerce ul.products') || document.body;
    if (!container || container._gphObserverAttached) return;

    const mo = new MutationObserver((mutations) => {
      mutations.forEach((m) => {
        m.addedNodes.forEach((node) => {
          if (!node || node.nodeType !== 1) return;

          if (m.type === 'attributes' && node.tagName === 'IMG') {
            handleImage(node);
            return;
          }

          if (node.matches && node.matches('img')) {
            if (
              node.matches(IMAGE_SELECTOR.split(',')[0].trim()) ||
              node.matches(IMAGE_SELECTOR.split(',')[1].trim())
            ) {
              handleImage(node);
              return;
            }
          }

          const imgs = node.querySelectorAll && node.querySelectorAll(IMAGE_SELECTOR);
          if (imgs && imgs.length) {
            imgs.forEach(handleImage);
          }
        });
      });
    });

    mo.observe(container, { childList: true, subtree: true, attributes: true, attributeFilter: ['src', 'data-src', 'data-lazy-src', 'data-srcset'] });
    container._gphObserverAttached = true;
  };

  /* ========================================================================
     FEATURE: SKELETON LOADER - END
     ======================================================================== */


  /* ========================================================================
     INITIALIZATION & SAFE-START
     ======================================================================== */

  // Run immediately (covers when script is loaded at end of body)
  initSkeletonLoader();
  observeNewImages();

  // Run when DOM is ready (covers many edge cases)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initSkeletonLoader();
      observeNewImages();
    });
  }

  // Run again on window load for additional safety
  window.addEventListener('load', () => {
    initSkeletonLoader();
  });

  /* ========================================================================
     END INITIALIZATION
     ======================================================================== */

})();