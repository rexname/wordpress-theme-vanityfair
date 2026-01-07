<?php
get_header();
?>

    <main id="main" class="site-main">
        <div class="container single-container">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    $categories = get_the_category();
                    $primary_category = ! empty( $categories ) ? $categories[0] : null;
                    $popular_meta_keys = array(
                        'post_views_count',
                        'post_views',
                        'views',
                        'view_count',
                        '_post_views',
                    );

                    $popular_query = null;
                    foreach ( $popular_meta_keys as $meta_key ) {
                        $candidate = new WP_Query( array(
                            'posts_per_page'      => 5,
                            'ignore_sticky_posts' => 1,
                            'post__not_in'        => array( (int) get_the_ID() ),
                            'meta_key'            => $meta_key,
                            'orderby'             => 'meta_value_num',
                            'order'               => 'DESC',
                        ) );
                        if ( $candidate->have_posts() ) {
                            $popular_query = $candidate;
                            break;
                        }
                    }

                    if ( ! $popular_query ) {
                        $popular_query = new WP_Query( array(
                            'posts_per_page'      => 5,
                            'ignore_sticky_posts' => 1,
                            'post__not_in'        => array( (int) get_the_ID() ),
                            'orderby'             => 'comment_count',
                            'order'               => 'DESC',
                        ) );
                    }
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>
                        <div class="single-hero">
                            <header class="single-header">
                                <?php if ( $primary_category ) : ?>
                                    <div class="single-kicker">
                                        <a href="<?php echo esc_url( get_category_link( $primary_category->term_id ) ); ?>">
                                            <?php echo esc_html( $primary_category->name ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php the_title( '<h1 class="single-title">', '</h1>' ); ?>

                                <div class="single-meta">
                                    By <?php the_author(); ?> <span class="single-meta-sep">|</span> <?php echo esc_html( get_the_date() ); ?>
                                </div>
                            </header>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="single-thumbnail">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="single-grid">
                            <div class="single-main">
                                <div class="single-content entry-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>

                            <aside class="single-sidebar" aria-label="Sidebar">
                                <div class="single-sidebar-top">
                                    <div class="single-sidebar-ad single-sidebar-ad-top">
                                        <div class="ad-slot ad-slot-vertical"></div>
                                    </div>

                                    <?php if ( $popular_query && $popular_query->have_posts() ) : ?>
                                        <section class="single-most-popular" aria-label="Most Popular">
                                            <div class="single-most-popular-header">MOST POPULAR</div>
                                            <div class="single-most-popular-list">
                                                <?php
                                                while ( $popular_query->have_posts() ) :
                                                    $popular_query->the_post();
                                                    ?>
                                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-popular-item' ); ?>>
                                                        <div class="single-popular-item-inner">
                                                            <?php if ( has_post_thumbnail() ) : ?>
                                                                <div class="single-popular-thumb">
                                                                    <a href="<?php the_permalink(); ?>">
                                                                        <?php the_post_thumbnail( 'thumbnail' ); ?>
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="single-popular-body">
                                                                <div class="single-popular-kicker">
                                                                    <?php
                                                                    $cats = get_the_category();
                                                                    if ( ! empty( $cats ) ) {
                                                                        echo esc_html( $cats[0]->name );
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <h3 class="single-popular-title">
                                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                                </h3>
                                                                <div class="single-popular-meta">By <?php the_author(); ?></div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                    <?php
                                                endwhile;
                                                wp_reset_postdata();
                                                ?>
                                            </div>
                                        </section>
                                    <?php endif; ?>
                                </div>

                                <div class="single-sidebar-bottom">
                                    <div class="single-sidebar-ad single-sidebar-ad-bottom">
                                        <div class="ad-slot ad-slot-vertical"></div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </article>

                    <?php
                    $readmore_primary_cat_id = $primary_category ? (int) $primary_category->term_id : 0;
                    $readmore_args = array(
                        'post_type'           => 'post',
                        'posts_per_page'      => 16,
                        'ignore_sticky_posts' => 1,
                        'post__not_in'        => array( (int) get_the_ID() ),
                    );

                    if ( $readmore_primary_cat_id ) {
                        $readmore_args['cat'] = $readmore_primary_cat_id;
                    }

                    $readmore_query = new WP_Query( $readmore_args );

                    if ( $readmore_primary_cat_id && ! $readmore_query->have_posts() ) {
                        wp_reset_postdata();
                        $readmore_args_fallback = $readmore_args;
                        unset( $readmore_args_fallback['cat'] );
                        $readmore_query = new WP_Query( $readmore_args_fallback );
                    }
                    ?>

                    <?php if ( $readmore_query->have_posts() ) : ?>
                        <section class="single-readmore" aria-label="Read More">
                            <div class="single-readmore-header"><span>READ MORE</span></div>
                            <div class="single-readmore-grid">
                                <?php
                                while ( $readmore_query->have_posts() ) :
                                    $readmore_query->the_post();
                                    $readmore_cats = get_the_category();
                                    $readmore_cat = ! empty( $readmore_cats ) ? $readmore_cats[0] : null;
                                    ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-readmore-card' ); ?>>
                                        <div class="single-readmore-media">
                                            <a href="<?php the_permalink(); ?>" class="single-readmore-media-link" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                                                <?php
                                                if ( has_post_thumbnail() ) {
                                                    the_post_thumbnail( 'medium_large', array( 'class' => 'single-readmore-img' ) );
                                                }
                                                ?>
                                            </a>
                                        </div>
                                        <div class="single-readmore-body">
                                            <?php if ( $readmore_cat ) : ?>
                                                <div class="single-readmore-kicker"><?php echo esc_html( $readmore_cat->name ); ?></div>
                                            <?php endif; ?>
                                            <h3 class="single-readmore-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <div class="single-readmore-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 22 ) ); ?></div>
                                            <div class="single-readmore-meta">By <?php the_author(); ?></div>
                                        </div>
                                    </article>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <section class="single-bottom-ads" aria-label="Iklan">
                        <div class="single-bottom-ad">
                            <div class="ad-slot ad-slot-horizontal"></div>
                        </div>
                        <div class="single-bottom-ad">
                            <div class="ad-slot ad-slot-horizontal"></div>
                        </div>
                    </section>

                    <?php
                endwhile;
            else :
                get_template_part( 'template-parts/content', 'none' );
            endif;
            ?>
        </div>
    </main><!-- #main -->

<?php
get_footer();
