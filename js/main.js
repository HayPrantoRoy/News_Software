(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);


    // Fixed Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').addClass('shadow-sm').css('top', '0px');
        } else {
            $('.sticky-top').removeClass('shadow-sm').css('top', '-200px');
        }
    });
    
    
   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Latest-news-carousel
    $(".latest-news-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 2000,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
           
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            },
            1200:{
                items:4
            }
        }
    });


    // What's New carousel
    $(".whats-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 2000,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:2
            },
            1200:{
                items:2
            }
        }
    });



    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $('.btn-play').click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $('#videoModal').on('shown.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
        })

        $('#videoModal').on('hide.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc);
        })
    });



})(jQuery);


 function openSidebarPanel() {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const overlay = document.getElementById('sidebarOverlayBg');
            
            sidebar.classList.add('sidebar-active');
            overlay.classList.add('overlay-active');
            
            // Prevent body scroll when sidebar is open
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarPanel() {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const overlay = document.getElementById('sidebarOverlayBg');
            
            sidebar.classList.remove('sidebar-active');
            overlay.classList.remove('overlay-active');
            document.body.style.overflow = 'auto';
        }

        function toggleMobileSearch() {
            // Mobile search functionality
            alert('Search functionality would be implemented here');
        }

        // Update date display
        function updateDateDisplay() {
            const dateElement = document.getElementById('currentDateElement');
            if (!dateElement) return; // Element not found on this page
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            dateElement.textContent = now.toLocaleDateString('en-US', options);
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('mobileSidebarPanel');
            const menuToggle = document.querySelector('.mobile-menu-button');
            
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                closeSidebarPanel();
            }
        });

        // Initialize
        updateDateDisplay();


            const ticker = document.getElementById('tickerMove');

    ticker.addEventListener('mouseenter', () => {
      ticker.classList.add('paused');
    });

    ticker.addEventListener('mouseleave', () => {
      ticker.classList.remove('paused');
    });


// Show horizontal scrollbar on the nav only while the user is actively scrolling/touching it
(function () {
    var nav = document.querySelector('.primary-nav-menu');
    if (!nav) return;

    var scrollTimeout;

    function startScrolling() {
        nav.classList.add('scrolling');
        window.clearTimeout(scrollTimeout);
        scrollTimeout = window.setTimeout(function () {
            nav.classList.remove('scrolling');
        }, 600); // hide scrollbar 600ms after last scroll event
    }

    // Pointer/touch and wheel events
    nav.addEventListener('wheel', startScrolling, {passive: true});
    nav.addEventListener('touchstart', startScrolling, {passive: true});
    nav.addEventListener('touchmove', startScrolling, {passive: true});
    nav.addEventListener('pointerdown', startScrolling, {passive: true});
    nav.addEventListener('pointermove', startScrolling, {passive: true});

    // Also show while hovering with mouse
    nav.addEventListener('mouseenter', function () {
        nav.classList.add('scrolling');
    });
    nav.addEventListener('mouseleave', function () {
        nav.classList.remove('scrolling');
    });
})();


// Overlay thumb that mimics mobile native scroll indicator
(function () {
    var nav = document.querySelector('.primary-nav-menu');
    if (!nav) return;

    // create thumb element
    var thumb = document.createElement('div');
    thumb.className = 'nav-scroll-thumb';
    nav.appendChild(thumb);

    var hideTimer;

    function updateThumb() {
        var visibleRatio = nav.clientWidth / nav.scrollWidth;
        var thumbWidth = Math.max(24, nav.clientWidth * visibleRatio);
        var maxScrollLeft = nav.scrollWidth - nav.clientWidth;
        var left = (nav.scrollLeft / (maxScrollLeft || 1)) * (nav.clientWidth - thumbWidth);

        thumb.style.width = thumbWidth + 'px';
        thumb.style.transform = 'translateX(' + (left || 0) + 'px)';
    }

    function showThumb() {
        thumb.classList.add('show');
        window.clearTimeout(hideTimer);
        hideTimer = window.setTimeout(function () {
            thumb.classList.remove('show');
        }, 700);
    }

    function onScroll() {
        updateThumb();
        showThumb();
        nav.classList.add('scrolling');
        window.clearTimeout(hideTimer);
        hideTimer = window.setTimeout(function () {
            nav.classList.remove('scrolling');
            thumb.classList.remove('show');
        }, 700);
    }

    // initial
    updateThumb();

    nav.addEventListener('scroll', onScroll, {passive: true});
    nav.addEventListener('wheel', onScroll, {passive: true});
    nav.addEventListener('touchstart', onScroll, {passive: true});
    nav.addEventListener('touchmove', onScroll, {passive: true});
    window.addEventListener('resize', function () {
        updateThumb();
    });

    // show on mouse enter
    nav.addEventListener('mouseenter', function () {
        updateThumb();
        thumb.classList.add('show');
    });
    nav.addEventListener('mouseleave', function () {
        thumb.classList.remove('show');
    });
})();