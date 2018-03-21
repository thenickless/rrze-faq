// Smooth scrolling for anchor-links (excluding accordion-toggles)

jQuery(document).ready(function($) {	
    $('a[href*="#"]:not([href="#"]):not(.accordion-toggle):not(.accordion-tabs-nav-toggle)').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
        if (target.length) {
            $('html,body').animate({
                    scrollTop: target.offset().top - 185
                    }, 1000);
                    return false;
            }
        }
    });
});