<?php
/**
 * Astra Child Theme – Custom 404 Page
 * 
 * Last Update: 09 Jan, 26
 * @package astra-child
 */

// 1. Send a proper 404 header (Safety check)
status_header( 404 );

get_header();
?>

<main class="ast-container" id="primary">
  <section class="gph-404 gph-container">
    
    <h1 class="gph-404-title">
      <?php esc_html_e( '404 – Page Not Found', 'astra-child' ); ?>
    </h1>

    <p class="gph-404-text">
      <?php esc_html_e( 'We can’t find the page you’re looking for. Search for products below or return to the homepage.', 'astra-child' ); ?>
    </p>

    <div class="gph-404-search">
      <?php 
      /**
       * Speed Tip: Using the native search form is faster than 
       * any plugin-based search shortcode.
       */
      get_search_form(); 
      ?>
    </div>

    <div class="gph-404-actions">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button back-to-home">
        <?php esc_html_e( 'Back to Home', 'astra-child' ); ?>
      </a>
    </div>

  </section>
</main>

<?php
get_footer();