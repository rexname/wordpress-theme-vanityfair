<?php
get_header();
?>

    <main id="main" class="site-main">
        <?php
        $search_query = get_search_query( false );
        $search_title = $search_query !== '' ? sprintf( 'Search: %s', $search_query ) : 'Search';
        ?>
        <div class="container category-archive category-archive-v2">
            <header class="category-archive-header">
                <h1 class="category-archive-title"><?php echo esc_html( $search_title ); ?></h1>
            </header>

            <?php
            $hero_query = new WP_Query( array(
                'posts_per_page'      => 1,
                'ignore_sticky_posts' => 1,
                'post_status'         => 'publish',
                's'                   => $search_query,
            ) );

            if ( ! $hero_query->have_posts() ) {
                get_template_part( 'template-parts/content', 'none' );
                wp_reset_postdata();
            } else {
                $hero_query->the_post();
                $hero_id = (int) get_the_ID();
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'category-hero' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="category-hero-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <header class="category-hero-header">
                        <div class="category-hero-kicker"><?php echo esc_html( $search_title ); ?></div>
                        <?php
                        the_title( '<h2 class="category-hero-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
                        ?>
                        <div class="category-hero-meta">By <?php the_author(); ?></div>
                    </header>

                    <div class="category-hero-excerpt">
                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 28 ) ); ?>
                    </div>
                </article>

                <?php
                wp_reset_postdata();

                $per_box = 5;
                $initial_boxes = 3;
                $initial_total = $per_box * $initial_boxes;

                $initial_query = new WP_Query( array(
                    'posts_per_page'      => $initial_total + 1,
                    'ignore_sticky_posts' => 1,
                    'post_status'         => 'publish',
                    's'                   => $search_query,
                    'post__not_in'        => array( $hero_id ),
                    'offset'              => 0,
                ) );

                $initial_posts = $initial_query->posts;
                $has_more = count( $initial_posts ) > $initial_total;
                $render_posts = array_slice( $initial_posts, 0, $initial_total );
                $rendered_count = count( $render_posts );
                wp_reset_postdata();
                ?>

                <?php if ( ! empty( $render_posts ) ) : ?>
                    <div class="category-boxes" data-category-boxes>
                        <?php foreach ( array_chunk( $render_posts, $per_box ) as $box_posts ) : ?>
                            <div class="category-box">
                                <div class="category-box-grid">
                                    <section class="category-posts">
                                        <div class="category-list" data-category-list>
                                            <?php foreach ( $box_posts as $post ) : ?>
                                                <?php setup_postdata( $post ); ?>
                                                <article id="post-<?php the_ID(); ?>" <?php post_class( 'category-list-item' ); ?>>
                                                    <?php if ( has_post_thumbnail() ) : ?>
                                                        <div class="category-list-thumbnail">
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
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
                                            <?php endforeach; ?>
                                            <?php wp_reset_postdata(); ?>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $has_more ) : ?>
                    <div class="category-load-more-wrap">
                        <button
                            type="button"
                            class="category-load-more js-search-load-more"
                            data-s="<?php echo esc_attr( $search_query ); ?>"
                            data-offset="<?php echo esc_attr( $rendered_count ); ?>"
                            data-hero-id="<?php echo esc_attr( $hero_id ); ?>"
                            data-nonce="<?php echo esc_attr( wp_create_nonce( 'vanityfair_load_more_search' ) ); ?>"
                        >
                            Load More
                        </button>
                    </div>
                <?php endif; ?>
                <?php
            }
            ?>
        </div>
    </main>

<?php
get_footer();
