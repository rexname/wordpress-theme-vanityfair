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

    <header id="masthead" class="site-header">
        <div class="header-container">
            <div class="header-left">
                
            </div>
            <div class="header-center">
                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
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
        <nav id="site-navigation" class="main-navigation">
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'fallback_cb'    => false,
                ) );
            ?>
        </nav>
    </header>

    <div id="content" class="site-content">
