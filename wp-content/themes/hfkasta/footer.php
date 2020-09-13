    <div id="overlay"></div>

    <footer>

        <a href="https://www.instagram.com/hfk_asta" target="_blank">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/instagram.svg">
        </a>
        <a href="https://t.me/hfkasta" target="_blank">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/telegram.svg">
        </a>
        <a href="https://www.facebook.com/asta.hfk.bremen/" target="_blank">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/facebook.svg">
        </a>

        <a href="get_permalink( get_page_by_path( 'imprint' ) );" class="imprint">Imprint</a>

    </footer>
    
    <script>
        var forEach=function(t,o,r){if("[object Object]"===Object.prototype.toString.call(t))for(var c in t)Object.prototype.hasOwnProperty.call(t,c)&&o.call(r,t[c],c,t);else for(var e=0,l=t.length;l>e;e++)o.call(r,t[e],e,t)};

        // Toogle Classes on hamburger Menu
        var hamburgers = document.querySelectorAll(".hamburger");
        var nav = document.getElementById("nav");
        var overlay = document.getElementById("overlay");

        if (hamburgers.length > 0) {
            forEach(hamburgers, function(hamburger) {
                hamburger.addEventListener("click", function() {
                    this.classList.toggle("is-active");
                    nav.classList.toggle("is-open");
                    overlay.classList.toggle("open");
                }, false);
            });
        }

        overlay.addEventListener("click", function() {
            forEach(hamburgers, function(hamburger) {
                hamburger.classList.toggle("is-active");
            });
            
            mobileNav.classList.toggle("is-open");
            overlay.classList.toggle("open");
        });
 
        // Move Hamburger Menu
        window.onscroll = function() {
            if (window.pageYOffset <= 100) {
                let hamburgerTop = 0;

                if (window.pageYOffset <= 40) {
                    hamburgerTop = (40 - window.pageYOffset);
                } else {
                    hamburgerTop = 0;
                }

                document.getElementById('hamburger').style.top = hamburgerTop + 'px';
            }
        };

        // Open Sub-Menus
        var navParents = document.querySelectorAll(".menu-item-has-children");

        if (navParents.length > 0) {
            forEach(navParents, function(navParent) {
                navParent.addEventListener("click", function() {
                    if (window.innerWidth < 786) {
                        this.classList.toggle("is-open");
                    }
                }, false);
            });
        }

        // Set animation values for ticker
        window.onresize = setTickerValues;
        window.onload = setTickerValues;

        function setTickerValues() {
            let ticker = document.getElementById('ticker');
            let tickerWidth = -ticker.clientWidth/2;

            document.documentElement.style.setProperty('--tickerWidth', tickerWidth + 'px');
        }
    </script>
    <style>
        @-webkit-keyframes ticker {
            0% {
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
                visibility: visible;
            }

            100% {
                -webkit-transform: translate3d(var(--tickerWidth), 0, 0);
                transform: translate3d(var(--tickerWidth), 0, 0);
            }
        }

        @keyframes ticker {
            0% {
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
                visibility: visible;
            }

            100% {
                -webkit-transform: translate3d(var(--tickerWidth), 0, 0);
                transform: translate3d(var(--tickerWidth), 0, 0);
            }
        }
    </style>
    </body>

    <?php wp_footer(); ?>

</html>