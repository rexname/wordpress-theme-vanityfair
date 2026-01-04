( function() {
    const menuToggle = document.querySelector( '.menu-toggle' );
    const siteNavigation = document.querySelector( '#site-navigation' );
    const siteHeader = document.querySelector( '.site-header' );
    const siteOverlay = document.querySelector( '.site-overlay' );

    if ( menuToggle && siteNavigation && siteHeader && siteOverlay ) {
        const menuText = menuToggle.querySelector( '.menu-text' );
        const menuIcon = menuToggle.querySelector( 'svg' );

        const closeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41z"></path></svg>`;
        const menuIconHTML = menuIcon ? menuIcon.outerHTML : '';

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

            if ( menuText ) {
                if ( ! isExpanded ) {
                    menuText.textContent = 'Close';
                } else {
                    menuText.textContent = 'Menu';
                }
            }

            const currentIcon = menuToggle.querySelector( 'svg' );
            if ( currentIcon ) {
                if ( ! isExpanded ) {
                    currentIcon.outerHTML = closeIcon;
                } else if ( menuIconHTML ) {
                    currentIcon.outerHTML = menuIconHTML;
                }
            }
        } );

        siteOverlay.addEventListener( 'click', function() {
            menuToggle.click();
        } );

        window.addEventListener( 'load', updateMenuPosition );
        window.addEventListener( 'resize', updateMenuPosition );

        let lastScrollTop = 0;
        const header = document.querySelector( '.site-header' );
        const categoryBar = document.querySelector( '.category-bar' );
        const headerOffset = header ? header.offsetTop : 0;
        let scrollPosOnCatBarHidden = 0;

        if ( header && categoryBar ) {
            window.addEventListener( 'scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const headerHeight = header.offsetHeight;

                if ( scrollTop > headerOffset ) {
                    header.classList.add( 'header-fixed' );
                    categoryBar.classList.add( 'category-bar-fixed' );
                } else {
                    header.classList.remove( 'header-fixed', 'header-hidden' );
                    categoryBar.classList.remove( 'category-bar-fixed', 'category-bar-hidden' );
                    scrollPosOnCatBarHidden = 0;
                }

                if ( scrollTop > headerOffset && scrollTop > lastScrollTop ) {
                    if ( ! categoryBar.classList.contains( 'category-bar-hidden' ) ) {
                        categoryBar.classList.add( 'category-bar-hidden' );
                        scrollPosOnCatBarHidden = scrollTop;
                    } else {
                        if ( scrollPosOnCatBarHidden > 0 && scrollTop > scrollPosOnCatBarHidden + ( headerHeight * 3 ) ) {
                            header.classList.add( 'header-hidden' );
                        }
                    }
                } else if ( scrollTop < lastScrollTop ) {
                    if ( header.classList.contains( 'header-hidden' ) ) {
                        header.classList.remove( 'header-hidden' );
                    } else {
                        if ( categoryBar.classList.contains( 'category-bar-hidden' ) ) {
                            categoryBar.classList.remove( 'category-bar-hidden' );
                            scrollPosOnCatBarHidden = 0;
                        }
                    }
                }

                if ( categoryBar.classList.contains( 'category-bar-fixed' ) ) {
                    if ( header.classList.contains( 'header-hidden' ) ) {
                        categoryBar.style.top = '0px';
                    } else {
                        categoryBar.style.top = `${headerHeight}px`;
                    }
                } else {
                    categoryBar.style.top = '';
                }

                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, false );
        }
    }

    function initBestofCarousel() {
        const section = document.querySelector( '.bestof-section' );
        if ( ! section ) return;

        const track = section.querySelector( '.bestof-track' );
        const prev = section.querySelector( '.bestof-prev' );
        const next = section.querySelector( '.bestof-next' );
        const dotsWrap = section.querySelector( '.bestof-dots' );

        if ( ! track || ! prev || ! next || ! dotsWrap ) return;

        let rafId = 0;

        function getGap() {
            const styles = window.getComputedStyle( track );
            const gap = parseFloat( styles.gap || styles.columnGap || '0' );
            return Number.isFinite( gap ) ? gap : 0;
        }

        function getStep() {
            const first = track.querySelector( '.bestof-card' );
            if ( ! first ) return track.clientWidth;
            const gap = getGap();
            const itemWidth = first.getBoundingClientRect().width;
            const perPage = Math.max( 1, Math.round( track.clientWidth / ( itemWidth + gap ) ) );
            return ( itemWidth + gap ) * perPage;
        }

        function clampIndex( index, total ) {
            return Math.max( 0, Math.min( index, Math.max( 0, total - 1 ) ) );
        }

        function getTotalPages() {
            const first = track.querySelector( '.bestof-card' );
            const items = track.querySelectorAll( '.bestof-card' );
            if ( ! first || items.length === 0 ) return 0;

            const gap = getGap();
            const itemWidth = first.getBoundingClientRect().width;
            const perPage = Math.max( 1, Math.round( track.clientWidth / ( itemWidth + gap ) ) );
            return Math.ceil( items.length / perPage );
        }

        function getIndex() {
            const step = getStep();
            if ( step <= 0 ) return 0;
            return Math.round( track.scrollLeft / step );
        }

        function updateControls() {
            const total = getTotalPages();
            if ( total <= 1 ) {
                prev.disabled = true;
                next.disabled = true;
                dotsWrap.innerHTML = '';
                return;
            }

            const idx = clampIndex( getIndex(), total );
            prev.disabled = idx <= 0;
            next.disabled = idx >= total - 1;

            const dots = dotsWrap.querySelectorAll( '.bestof-dot' );
            dots.forEach( function( dot, i ) {
                if ( i === idx ) {
                    dot.classList.add( 'is-active' );
                } else {
                    dot.classList.remove( 'is-active' );
                }
            } );
        }

        function buildDots() {
            const total = getTotalPages();
            dotsWrap.innerHTML = '';
            if ( total <= 1 ) return;

            for ( let i = 0; i < total; i++ ) {
                const btn = document.createElement( 'button' );
                btn.type = 'button';
                btn.className = 'bestof-dot' + ( i === 0 ? ' is-active' : '' );
                btn.setAttribute( 'aria-label', `Slide ${i + 1}` );
                btn.addEventListener( 'click', function() {
                    track.scrollTo( { left: getStep() * i, behavior: 'smooth' } );
                } );
                dotsWrap.appendChild( btn );
            }
        }

        prev.addEventListener( 'click', function() {
            track.scrollBy( { left: -getStep(), behavior: 'smooth' } );
        } );

        next.addEventListener( 'click', function() {
            track.scrollBy( { left: getStep(), behavior: 'smooth' } );
        } );

        track.addEventListener( 'scroll', function() {
            if ( rafId ) cancelAnimationFrame( rafId );
            rafId = requestAnimationFrame( updateControls );
        } );

        window.addEventListener( 'resize', function() {
            buildDots();
            updateControls();
        } );

        buildDots();
        updateControls();
    }

    function initCategoryLoadMore() {
        const btn = document.querySelector( '.js-category-load-more' );
        if ( ! btn ) return;

        const boxesWrap = document.querySelector( '[data-category-boxes]' );
        if ( ! boxesWrap ) return;

        const ajaxUrl = window.vanityfairTheme && window.vanityfairTheme.ajaxUrl;
        if ( ! ajaxUrl ) return;

        let isLoading = false;

        function setLoading( loading ) {
            isLoading = loading;
            btn.disabled = loading;
            if ( loading ) {
                btn.classList.add( 'is-loading' );
            } else {
                btn.classList.remove( 'is-loading' );
            }
        }

        btn.addEventListener( 'click', function() {
            if ( isLoading ) return;

            setLoading( true );

            const params = new URLSearchParams();
            params.set( 'action', 'vanityfair_load_more_category' );
            params.set( 'nonce', btn.dataset.nonce || '' );
            params.set( 'termId', btn.dataset.termId || '' );
            params.set( 'offset', btn.dataset.offset || '0' );
            params.set( 'heroId', btn.dataset.heroId || '0' );

            fetch( ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                },
                body: params.toString(),
            } )
                .then( function( res ) {
                    return res.json();
                } )
                .then( function( data ) {
                    if ( ! data || ! data.success || ! data.data ) {
                        throw new Error( 'request_failed' );
                    }

                    const html = data.data.html || '';
                    if ( html ) {
                        const tmp = document.createElement( 'div' );
                        tmp.innerHTML = html;
                        while ( tmp.firstChild ) {
                            boxesWrap.appendChild( tmp.firstChild );
                        }
                    }

                    const nextOffset = typeof data.data.nextOffset === 'number' ? data.data.nextOffset : parseInt( btn.dataset.offset || '0', 10 ) + 5;
                    btn.dataset.offset = String( nextOffset );

                    if ( ! data.data.hasMore ) {
                        btn.remove();
                    }
                } )
                .catch( function() {
                    setLoading( false );
                } )
                .finally( function() {
                    if ( document.body.contains( btn ) ) {
                        setLoading( false );
                    }
                } );
        } );
    }

    initBestofCarousel();
    initCategoryLoadMore();
} )();
