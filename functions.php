<?php

if ( ! function_exists( 'vanityfair_seo_setup' ) ) :
    function vanityfair_seo_setup() {
        // Make theme available for translation.
        load_theme_textdomain( 'vanityfair-seo', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        add_theme_support( 'custom-logo' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'primary' => esc_html__( 'Primary Menu', 'vanityfair-seo' ),
            'footer_legal' => esc_html__( 'Footer Legal Menu', 'vanityfair-seo' ),
            'footer_contact' => esc_html__( 'Footer Contact Menu', 'vanityfair-seo' ),
            'header-utility' => esc_html__( 'Header Utility Menu', 'vanityfair-seo' ),
        ) );

        // Switch default core markup for search form, comment form, and comments to output valid HTML5.
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );
    }
endif;
add_action( 'after_setup_theme', 'vanityfair_seo_setup' );

function vanityfair_seo_scripts() {
    wp_enqueue_style( 'vanityfair-fonts-gfs-didot', 'https://fonts.googleapis.com/css2?family=GFS+Didot&display=swap', array(), null );

    // Enqueue main stylesheet.
    wp_enqueue_style( 'vanityfair-seo-style', get_stylesheet_uri() );

    // Enqueue main javascript file.
    wp_enqueue_script( 'vanityfair-seo-main', get_template_directory_uri() . '/js/main.js', array(), '1.0', true );
    wp_localize_script( 'vanityfair-seo-main', 'vanityfairTheme', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'vanityfair_seo_scripts' );

function vanityfair_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' !== $relation_type ) {
        return $urls;
    }

    $urls[] = 'https://fonts.googleapis.com';
    $urls[] = array(
        'href'        => 'https://fonts.gstatic.com',
        'crossorigin' => 'anonymous',
    );

    return $urls;
}
add_filter( 'wp_resource_hints', 'vanityfair_resource_hints', 10, 2 );

function vanityfair_load_more_category() {
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'vanityfair_load_more_category' ) ) {
        wp_send_json_error( array( 'message' => 'invalid_nonce' ), 403 );
    }

    $term_id = isset( $_POST['termId'] ) ? absint( $_POST['termId'] ) : 0;
    $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
    $hero_id = isset( $_POST['heroId'] ) ? absint( $_POST['heroId'] ) : 0;

    if ( ! $term_id ) {
        wp_send_json_error( array( 'message' => 'invalid_term' ), 400 );
    }

    $query_args = array(
        'posts_per_page'      => 6,
        'ignore_sticky_posts' => 1,
        'cat'                 => $term_id,
        'offset'              => $offset,
    );
    if ( $hero_id ) {
        $query_args['post__not_in'] = array( $hero_id );
    }

    $q = new WP_Query( $query_args );
    $has_more = $q->post_count > 5;

    $html = '';
    if ( $q->have_posts() ) {
        ob_start();
        $count = 0;
        $term = get_term( $term_id, 'category' );
        $kicker = ( $term && ! is_wp_error( $term ) ) ? $term->name : '';

        ?>
        <div class="category-box">
            <div class="category-box-grid">
                <section class="category-posts">
                    <div class="category-list">
                        <?php
                        while ( $q->have_posts() && $count < 5 ) {
                            $q->the_post();
                            $count++;
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'category-list-item' ); ?>>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="category-list-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'large' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="category-list-body">
                                    <div class="category-list-kicker"><?php echo esc_html( $kicker ); ?></div>
                                    <?php
                                    the_title( '<h3 class="category-list-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' );
                                    ?>
                                    <div class="category-list-excerpt">
                                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
                                    </div>
                                    <div class="category-list-meta">By <?php the_author(); ?></div>
                                </div>
                            </article>
                            <?php
                        }
                        ?>
                    </div>
                </section>

                <aside class="category-ad" aria-label="Iklan">
                    <div class="ad-slot ad-slot-vertical"></div>
                </aside>
            </div>
        </div>
        <?php

        wp_reset_postdata();
        $html = ob_get_clean();
    }

    wp_send_json_success( array(
        'html'      => $html,
        'hasMore'   => $has_more,
        'nextOffset'=> $offset + 5,
    ) );
}
add_action( 'wp_ajax_vanityfair_load_more_category', 'vanityfair_load_more_category' );
add_action( 'wp_ajax_nopriv_vanityfair_load_more_category', 'vanityfair_load_more_category' );

function vanityfair_load_more_search() {
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'vanityfair_load_more_search' ) ) {
        wp_send_json_error( array( 'message' => 'invalid_nonce' ), 403 );
    }

    $search_query = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
    $offset       = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
    $hero_id      = isset( $_POST['heroId'] ) ? absint( $_POST['heroId'] ) : 0;

    if ( $search_query === '' ) {
        wp_send_json_error( array( 'message' => 'invalid_search' ), 400 );
    }

    $query_args = array(
        'posts_per_page'      => 6,
        'ignore_sticky_posts' => 1,
        'post_status'         => 'publish',
        's'                   => $search_query,
        'offset'              => $offset,
    );
    if ( $hero_id ) {
        $query_args['post__not_in'] = array( $hero_id );
    }

    $q        = new WP_Query( $query_args );
    $has_more = $q->post_count > 5;

    $html = '';
    if ( $q->have_posts() ) {
        ob_start();
        $count       = 0;
        $search_title = $search_query !== '' ? sprintf( 'Search: %s', $search_query ) : 'Search';
        ?>
        <div class="category-box">
            <div class="category-box-grid">
                <section class="category-posts">
                    <div class="category-list">
                        <?php
                        while ( $q->have_posts() && $count < 5 ) {
                            $q->the_post();
                            $count++;
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'category-list-item' ); ?>>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="category-list-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'large' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="category-list-body">
                                    <div class="category-list-kicker"><?php echo esc_html( $search_title ); ?></div>
                                    <?php
                                    the_title( '<h3 class="category-list-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' );
                                    ?>
                                    <div class="category-list-excerpt">
                                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
                                    </div>
                                    <div class="category-list-meta">By <?php the_author(); ?></div>
                                </div>
                            </article>
                            <?php
                        }
                        ?>
                    </div>
                </section>

                <aside class="category-ad" aria-label="Iklan">
                    <div class="ad-slot ad-slot-vertical"></div>
                </aside>
            </div>
        </div>
        <?php

        wp_reset_postdata();
        $html = ob_get_clean();
    }

    wp_send_json_success( array(
        'html'       => $html,
        'hasMore'    => $has_more,
        'nextOffset' => $offset + 5,
    ) );
}
add_action( 'wp_ajax_vanityfair_load_more_search', 'vanityfair_load_more_search' );
add_action( 'wp_ajax_nopriv_vanityfair_load_more_search', 'vanityfair_load_more_search' );

function vanityfair_get_svg( $icon_name ) {
    $svg_icons = array(
        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12,2C6.477,2,2,6.477,2,12c0,5.013,3.693,9.153,8.505,9.876V14.65H8.031v-2.629h2.474v-1.749 c0-2.896,1.411-4.167,3.818-4.167c1.153,0,1.762,0.085,2.051,0.124v2.294h-1.642c-1.022,0-1.379,0.969-1.379,2.061v1.437h2.995 l-0.406,2.629h-2.588v7.247C18.235,21.236,22,17.062,22,12C22,6.477,17.523,2,12,2z"></path></svg>',
        'twitter'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13.6468 10.4686L20.9321 2H19.2057L12.8799 9.3532L7.82741 2H2L9.6403 13.1193L2 22H3.72649L10.4068 14.2348L15.7425 22H21.5699L13.6464 10.4686H13.6468ZM11.2821 13.2173L10.508 12.1101L4.34857 3.29968H7.00037L11.9711 10.4099L12.7452 11.5172L19.2066 20.7594H16.5548L11.2821 13.2177V13.2173Z"></path></svg>',
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M 8 3 C 5.239 3 3 5.239 3 8 L 3 16 C 3 18.761 5.239 21 8 21 L 16 21 C 18.761 21 21 18.761 21 16 L 21 8 C 21 5.239 18.761 3 16 3 L 8 3 z M 18 5 C 18.552 5 19 5.448 19 6 C 19 6.552 18.552 7 18 7 C 17.448 7 17 6.552 17 6 C 17 5.448 17.448 5 18 5 z M 12 7 C 14.761 7 17 9.239 17 12 C 17 14.761 14.761 17 12 17 C 9.239 17 7 14.761 7 12 C 7 9.239 9.239 7 12 7 z M 12 9 A 3 3 0 0 0 9 12 A 3 3 0 0 0 12 15 A 3 3 0 0 0 15 12 A 3 3 0 0 0 12 9 z"></path></svg>',
        'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12,2C6.477,2,2,6.477,2,12c0,4.237,2.636,7.855,6.356,9.312c-0.087-0.791-0.167-2.005,0.035-2.868 c0.182-0.78,1.172-4.971,1.172-4.971s-0.299-0.599-0.299-1.484c0-1.391,0.806-2.428,1.809-2.428c0.853,0,1.265,0.641,1.265,1.408 c0,0.858-0.546,2.141-0.828,3.329c-0.236,0.996,0.499,1.807,1.481,1.807c1.777,0,3.143-1.874,3.143-4.579 c0-2.394-1.72-4.068-4.177-4.068c-2.845,0-4.515,2.134-4.515,4.34c0,0.859,0.331,1.781,0.744,2.282 c0.082,0.099,0.093,0.186,0.069,0.287c-0.076,0.316-0.244,0.995-0.277,1.134c-0.043,0.183-0.145,0.222-0.334,0.133 c-1.249-0.582-2.03-2.408-2.03-3.874c0-3.154,2.292-6.052,6.608-6.052c3.469,0,6.165,2.472,6.165,5.776 c0,3.447-2.173,6.22-5.189,6.22c-1.013,0-1.966-0.527-2.292-1.148c0,0-0.502,1.909-0.623,2.378 c-0.226,0.868-0.835,1.958-1.243,2.622C9.975,21.843,10.969,22,12,22c5.522,0,10-4.478,10-10S17.523,2,12,2z"></path></svg>',
        'youtube'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M21.5825 7.2C21.4694 6.77454 21.2464 6.38627 20.936 6.0741C20.6256 5.76192 20.2386 5.53679 19.8137 5.42125C18.2537 5 12 5 12 5C12 5 5.74625 5 4.18625 5.42125C3.76143 5.53679 3.37443 5.76192 3.06401 6.0741C2.75359 6.38627 2.53064 6.77454 2.4175 7.2C2 8.77 2 12.045 2 12.045C2 12.045 2 15.32 2.4175 16.89C2.53045 17.3157 2.75331 17.7042 3.06375 18.0166C3.37418 18.329 3.76128 18.5544 4.18625 18.67C5.74625 19.0913 12 19.0913 12 19.0913C12 19.0913 18.2537 19.0913 19.8137 18.67C20.2387 18.5544 20.6258 18.329 20.9363 18.0166C21.2467 17.7042 21.4696 17.3157 21.5825 16.89C22 15.3212 22 12.045 22 12.045C22 12.045 22 8.77 21.5825 7.2ZM9.955 15.0187V9.07125L15.1813 12.0463L9.955 15.0187Z"></path></svg>',
        'menu'      => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>',
        'close'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41z"></path></svg>',
        'search'    => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14z"></path></svg>',
    );

    if ( ! isset( $svg_icons[ $icon_name ] ) ) {
        return '';
    }

    return $svg_icons[ $icon_name ];
}
