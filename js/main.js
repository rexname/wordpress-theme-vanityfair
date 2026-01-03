( function() {
    const menuToggle = document.querySelector( '.menu-toggle' );
    const siteNavigation = document.querySelector( '#site-navigation' );
    const siteHeader = document.querySelector( '.site-header' );
    const siteOverlay = document.querySelector( '.site-overlay' );
    const menuText = menuToggle.querySelector( '.menu-text' );
    const menuIcon = menuToggle.querySelector( 'svg' );

    if ( ! menuToggle || ! siteNavigation || ! siteHeader || ! siteOverlay ) {
        return;
    }

    const closeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41z"></path></svg>`;
    const menuIconHTML = menuIcon.outerHTML;

    function getScrollbarWidth() {
        return window.innerWidth - document.documentElement.clientWidth;
    }

    function updateMenuPosition() {
        const headerHeight = siteHeader.offsetHeight;
        siteNavigation.style.top = headerHeight + 'px';
    }

    menuToggle.addEventListener( 'click', function() {
        document.documentElement.classList.toggle( 'menu-open' );
        siteNavigation.classList.toggle( 'toggled-on' );
        siteOverlay.style.display = siteNavigation.classList.contains( 'toggled-on' ) ? 'block' : 'none';

        const isExpanded = menuToggle.getAttribute( 'aria-expanded' ) === 'true';
        menuToggle.setAttribute( 'aria-expanded', ! isExpanded );

        if ( ! isExpanded ) {
            menuText.textContent = 'Close';
            menuToggle.querySelector( 'svg' ).outerHTML = closeIcon;
        } else {
            menuText.textContent = 'Menu';
            menuToggle.querySelector( 'svg' ).outerHTML = menuIconHTML;
        }
    } );

    siteOverlay.addEventListener( 'click', function() {
        menuToggle.click();
    } );

    window.addEventListener( 'load', updateMenuPosition );
    window.addEventListener( 'resize', updateMenuPosition );

    let lastScrollTop = 0;
    const header = document.querySelector('.site-header');
    const categoryBar = document.querySelector('.category-bar');
    const headerOffset = header.offsetTop;
    let scrollPosOnCatBarHidden = 0;

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const headerHeight = header.offsetHeight;

        // Stick/unstick header and category bar
        if (scrollTop > headerOffset) {
            header.classList.add('header-fixed');
            categoryBar.classList.add('category-bar-fixed');
        } else {
            header.classList.remove('header-fixed', 'header-hidden');
            categoryBar.classList.remove('category-bar-fixed', 'category-bar-hidden');
            scrollPosOnCatBarHidden = 0;
        }

        // Show/hide logic for when the header is fixed
        if (scrollTop > headerOffset && scrollTop > lastScrollTop) {
            // Scrolling Down
            if (!categoryBar.classList.contains('category-bar-hidden')) {
                categoryBar.classList.add('category-bar-hidden');
                scrollPosOnCatBarHidden = scrollTop;
            } else {
                if (scrollPosOnCatBarHidden > 0 && scrollTop > scrollPosOnCatBarHidden + (headerHeight * 3)) {
                    header.classList.add('header-hidden');
                }
            }
        } else if (scrollTop < lastScrollTop) {
            // Scrolling Up
            if (header.classList.contains('header-hidden')) {
                header.classList.remove('header-hidden');
            } else {
                if (categoryBar.classList.contains('category-bar-hidden')) {
                    categoryBar.classList.remove('category-bar-hidden');
                    scrollPosOnCatBarHidden = 0;
                }
            }
        }

        // Adjust category bar position based on header state
        if (categoryBar.classList.contains('category-bar-fixed')) {
            if (header.classList.contains('header-hidden')) {
                categoryBar.style.top = '0px';
            } else {
                categoryBar.style.top = `${headerHeight}px`;
            }
        } else {
            categoryBar.style.top = ''; // Reset when not fixed
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, false);
} )();
