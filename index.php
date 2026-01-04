<?php
get_header();
?>

    <main id="main" class="site-main">

        <?php if ( is_home() || is_front_page() ) : ?>
            <div class="container">
                <div class="main-content-grid home-featured-grid">
                    <?php
                    $home_hero_query = new WP_Query( array(
                        'posts_per_page'      => 1,
                        'ignore_sticky_posts' => 1,
                    ) );
                    $home_hero_ids = wp_list_pluck( $home_hero_query->posts, 'ID' );

                    $home_center_secondary_query = new WP_Query( array(
                        'posts_per_page'      => 2,
                        'post__not_in'        => $home_hero_ids,
                        'ignore_sticky_posts' => 1,
                    ) );
                    $home_center_secondary_ids = wp_list_pluck( $home_center_secondary_query->posts, 'ID' );

                    $home_left_exclude_ids = array_merge( $home_hero_ids, $home_center_secondary_ids );
                    $home_left_query = new WP_Query( array(
                        'posts_per_page'      => 3,
                        'post__not_in'        => $home_left_exclude_ids,
                        'ignore_sticky_posts' => 1,
                    ) );
                    $home_left_ids = wp_list_pluck( $home_left_query->posts, 'ID' );

                    $home_right_exclude_ids = array_merge( $home_left_exclude_ids, $home_left_ids );
                    $home_latest_query = new WP_Query( array(
                        'posts_per_page'      => 6,
                        'post__not_in'        => $home_right_exclude_ids,
                        'ignore_sticky_posts' => 1,
                    ) );
                    $home_latest_ids = wp_list_pluck( $home_latest_query->posts, 'ID' );
                    ?>

                    <div class="main-column-left">
                        <?php
                        if ( $home_left_query->have_posts() ) :
                            while ( $home_left_query->have_posts() ) :
                                $home_left_query->the_post();
                                get_template_part( 'template-parts/content', get_post_format() );
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>

                    <div class="main-column-center">
                        <?php
                        if ( $home_hero_query->have_posts() ) :
                            while ( $home_hero_query->have_posts() ) :
                                $home_hero_query->the_post();
                                ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class( 'home-hero' ); ?>>
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail( 'large' ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <header class="entry-header">
                                        <div class="entry-meta entry-meta-category">
                                            <?php
                                            $categories = get_the_category();
                                            if ( ! empty( $categories ) ) {
                                                echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                                            }
                                            ?>
                                        </div>
                                        <?php
                                        the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                                        ?>
                                        <div class="entry-meta">
                                            By <?php the_author(); ?>
                                        </div>
                                    </header>

                                    <div class="entry-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </article>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>

                        <?php if ( $home_center_secondary_query->have_posts() ) : ?>
                            <div class="home-center-secondary">
                                <?php
                                while ( $home_center_secondary_query->have_posts() ) :
                                    $home_center_secondary_query->the_post();
                                    ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'home-center-card' ); ?>>
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail( 'medium_large' ); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <header class="entry-header">
                                            <div class="entry-meta entry-meta-category">
                                                <?php
                                                $categories = get_the_category();
                                                if ( ! empty( $categories ) ) {
                                                    echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                                            ?>
                                            <div class="entry-meta">
                                                By <?php the_author(); ?>
                                            </div>
                                        </header>
                                    </article>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="main-column-right">
                        <h2 class="sidebar-title">Latest Stories</h2>
                        <?php
                        if ( $home_latest_query->have_posts() ) :
                            while ( $home_latest_query->have_posts() ) :
                                $home_latest_query->the_post();
                                get_template_part( 'template-parts/content', 'sidebar' );
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <?php
            $most_visited_meta_keys = array(
                'post_views_count',
                'post_views',
                'views',
                'view_count',
                '_post_views',
            );

            $most_visited_query = null;

            foreach ( $most_visited_meta_keys as $meta_key ) {
                $candidate = new WP_Query( array(
                    'posts_per_page'      => 9,
                    'ignore_sticky_posts' => 1,
                    'meta_key'            => $meta_key,
                    'orderby'             => 'meta_value_num',
                    'order'               => 'DESC',
                ) );

                if ( $candidate->have_posts() ) {
                    $most_visited_query = $candidate;
                    break;
                }
            }

            if ( ! $most_visited_query ) {
                $most_visited_query = new WP_Query( array(
                    'posts_per_page'      => 9,
                    'ignore_sticky_posts' => 1,
                    'orderby'             => 'comment_count',
                    'order'               => 'DESC',
                ) );
            }

            $most_visited_ids = wp_list_pluck( $most_visited_query->posts, 'ID' );
            ?>

            <?php if ( $most_visited_query->have_posts() ) : ?>
                <section class="bestof-section">
                    <div class="bestof-container container">
                        <div class="bestof-header">
                            <div class="bestof-title">MOST VISITED</div>
                            <div class="bestof-controls">
                                <button class="bestof-control bestof-prev" type="button" aria-label="Sebelumnya" aria-controls="bestof-track">&lsaquo;</button>
                                <button class="bestof-control bestof-next" type="button" aria-label="Berikutnya" aria-controls="bestof-track">&rsaquo;</button>
                            </div>
                        </div>

                        <div class="bestof-carousel" data-carousel="bestof">
                            <div class="bestof-track" id="bestof-track" tabindex="0">
                                <?php
                                while ( $most_visited_query->have_posts() ) :
                                    $most_visited_query->the_post();
                                    ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'bestof-card' ); ?>>
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="bestof-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail( 'large' ); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <div class="bestof-body">
                                            <div class="bestof-kicker">
                                                <?php
                                                $categories = get_the_category();
                                                if ( ! empty( $categories ) ) {
                                                    echo esc_html( $categories[0]->name );
                                                }
                                                ?>
                                            </div>
                                            <h3 class="bestof-card-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                        </div>
                                    </article>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                            <div class="bestof-dots" aria-label="Navigasi slider"></div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php
            $home_used_ids = array_values( array_unique( array_merge(
                $home_hero_ids,
                $home_center_secondary_ids,
                $home_left_ids,
                $home_latest_ids,
                $most_visited_ids
            ) ) );

            $home_categories = get_terms( array(
                'taxonomy'   => 'category',
                'hide_empty' => 1,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ) );
            ?>

            <?php if ( ! empty( $home_categories ) && ! is_wp_error( $home_categories ) ) : ?>
                <?php foreach ( $home_categories as $home_category ) : ?>
                    <?php
                    if ( (int) $home_category->term_id === 1 || $home_category->slug === 'uncategorized' ) {
                        continue;
                    }

                    $home_category_query = new WP_Query( array(
                        'posts_per_page'      => 6,
                        'ignore_sticky_posts' => 1,
                        'cat'                 => (int) $home_category->term_id,
                        'post__not_in'        => $home_used_ids,
                    ) );

                    if ( ! $home_category_query->have_posts() ) {
                        wp_reset_postdata();
                        continue;
                    }

                    $home_category_ids = wp_list_pluck( $home_category_query->posts, 'ID' );
                    $home_used_ids = array_values( array_unique( array_merge( $home_used_ids, $home_category_ids ) ) );
                    ?>

                    <section class="home-category-section">
                        <div class="container category-archive">
                            <header class="category-archive-header">
                                <h2 class="category-archive-title">
                                    <a href="<?php echo esc_url( get_category_link( (int) $home_category->term_id ) ); ?>">
                                        <?php echo esc_html( $home_category->name ); ?>
                                    </a>
                                </h2>
                            </header>

                            <div class="category-archive-grid">
                                <section class="category-posts">
                                    <div class="category-posts-grid">
                                        <?php
                                        while ( $home_category_query->have_posts() ) :
                                            $home_category_query->the_post();
                                            ?>
                                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'category-card' ); ?>>
                                                <?php if ( has_post_thumbnail() ) : ?>
                                                    <div class="category-card-thumbnail">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php the_post_thumbnail( 'large' ); ?>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="category-card-body">
                                                    <div class="category-card-kicker"><?php echo esc_html( $home_category->name ); ?></div>
                                                    <?php
                                                    the_title( '<h3 class="category-card-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' );
                                                    ?>
                                                    <div class="category-card-excerpt">
                                                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
                                                    </div>
                                                    <div class="category-card-meta">By <?php the_author(); ?></div>
                                                </div>
                                            </article>
                                            <?php
                                        endwhile;
                                        wp_reset_postdata();
                                        ?>
                                    </div>
                                </section>

                                <aside class="category-ad" aria-label="Iklan">
                                    <div class="ad-slot ad-slot-vertical"></div>
                                </aside>
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php else : ?>
            <div class="container">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content', get_post_format() );
                    endwhile;
                    the_posts_pagination( array(
                        'mid_size'  => 1,
                        'prev_text' => '&lsaquo;',
                        'next_text' => '&rsaquo;',
                    ) );
                else :
                    get_template_part( 'template-parts/content', 'none' );
                endif;
                ?>
            </div>
        <?php endif; ?>

    </main><!-- #main -->

<?php
get_footer();
