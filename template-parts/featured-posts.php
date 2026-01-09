<?php
$featured_posts_query = new WP_Query( array(
    'posts_per_page'      => 5,
    'ignore_sticky_posts' => 1,
    'post__not_in'        => get_option( 'sticky_posts' ),
) );

if ( $featured_posts_query->have_posts() ) : ?>
    <div class="featured-container">
        <div class="featured-grid">
            <?php
            $post_counter = 1;
            while ( $featured_posts_query->have_posts() ) :
                $featured_posts_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'featured-post-' . $post_counter ); ?>>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        </a>
                    </div>
                    <header class="entry-header">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                    </header>
                </article>
                <?php
                $post_counter++;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
<?php endif; ?>
