    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="footer-main">
            <div class="footer-column footer-brand">
                <span class="footer-logo"><?php bloginfo( 'name' ); ?></span>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><?php echo vanityfair_get_svg( 'facebook' ); ?></a>
                    <a href="#" aria-label="Twitter"><?php echo vanityfair_get_svg( 'twitter' ); ?></a>
                    <a href="#" aria-label="Instagram"><?php echo vanityfair_get_svg( 'instagram' ); ?></a>
                    <a href="#" aria-label="Pinterest"><?php echo vanityfair_get_svg( 'pinterest' ); ?></a>
                    <a href="#" aria-label="YouTube"><?php echo vanityfair_get_svg( 'youtube' ); ?></a>
                </div>
            </div>
            <div class="footer-column">
                <h3 class="footer-heading"><?php echo esc_html__( 'CATEGORIES', 'vanityfair-seo' ); ?></h3>
                <ul class="footer-menu">
                    <?php
                    wp_list_categories( array(
                        'title_li' => '',
                        'orderby'  => 'name',
                        'show_count' => 0,
                    ) );
                    ?>
                </ul>
            </div>
            <div class="footer-column">
                <h3 class="footer-heading"><?php echo esc_html__( 'CONTACT', 'vanityfair-seo' ); ?></h3>
                <?php
                if ( has_nav_menu( 'footer_contact' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'footer_contact',
                        'container'      => false,
                        'menu_class'     => 'footer-menu',
                    ) );
                } else {
                    // Fallback menu
                    echo '<ul class="footer-menu">';
                    echo '<li><a href="#">Contact VF</a></li>';
                    echo '<li><a href="#">Manage Account</a></li>';
                    echo '<li><a href="#">Advertising</a></li>';
                    echo '<li><a href="#">Careers</a></li>';
                    echo '</ul>';
                }
                ?>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-legal">
                 <?php
                if ( has_nav_menu( 'footer_legal' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'footer_legal',
                        'container'      => false,
                        'menu_class'     => 'footer-legal-menu',
                    ) );
                }
                ?>
            </div>
            <div class="footer-copyright">
                <p>
                    &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
                    <?php
                    printf(
                        esc_html__( 'Proudly powered by %s', 'vanityfair-seo' ),
                        '<a href="' . esc_url( __( 'https://wordpress.org/', 'vanityfair-seo' ) ) . '">WordPress</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </footer>
    <div class="site-overlay"></div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
