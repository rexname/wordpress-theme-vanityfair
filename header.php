<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'vanityfair-seo' ); ?></a>

    <div class="header-nav">
        <header id="masthead" class="site-header">
            <div class="header-container">
                <div class="header-left">
                    
                </div>
                <div class="header-center">
                    <div class="site-title">
                        <?php
                            if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                                the_custom_logo();
                            } elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
                                $site_icon_url = get_site_icon_url( 192 );
                                if ( $site_icon_url ) {
                                    ?>
                                    <a class="site-icon-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                        <img class="site-icon" src="<?php echo esc_url( $site_icon_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                                    </a>
                                    <?php
                                }
                            } else {
                                ?>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="header-right">
                    <nav id="header-utility-navigation" class="header-utility-navigation">
                        <?php
                            wp_nav_menu( array(
                                'theme_location' => 'header-utility',
                                'menu_id'        => 'header-utility-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                            ) );
                        ?>
                    </nav>
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-text"><?php esc_html_e( 'Menu', 'vanityfair-seo' ); ?></span>
                        <?php echo vanityfair_get_svg( 'menu' ); ?>
                    </button>
                </div>
            </div>
        </header><!-- #masthead -->

        <nav class="category-bar">
            <div class="category-bar-container">
                <ul>
                    <?php
                        wp_list_categories( array(
                            'title_li' => '',
                            'orderby'  => 'name',
                            'show_count' => 0,
                            'hierarchical' => 0,
                            'depth' => 1,
                            'hide_empty' => 1,
                        ) );
                    ?>
                </ul>
            </div>
        </nav>
    </div>

    <div class="site-overlay"></div>

    <nav id="site-navigation" class="main-navigation">
        <div class="search-container-nav">
            <?php get_search_form(); ?>
        </div>
        <ul>
        <?php
            wp_list_categories( array(
                'title_li' => '',
                'orderby'  => 'name',
                'show_count' => 0,
                'hierarchical' => 1,
            ) );
        ?>
        </ul>
    </nav><!-- #site-navigation -->

    <div id="content" class="site-content">
