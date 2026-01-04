<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="sidebar-item">
        <div class="entry-summary">
            <div class="entry-meta entry-meta-category">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                }
                ?>
            </div>
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-meta">
                By <?php the_author(); ?>
            </div>
        </div>

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</article>
