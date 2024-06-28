
$(document).ready(function () { 
    // nav link active class 
    $(".navbar-nav .nav-link").click(function(){
        $(".navbar-nav .nav-link").removeClass("active");
        $(this).addClass("active");
        // $(".navbar-collapse").slideToggle(); 
        $(this).parents(".navbar-collapse.show").slideToggle();
    }); 
    // $(".navbar-collapse.show .nav-link").click(function(){ 
    //     console.log('click');
    //     $(".navbar-collapse").slideToggle(); 
    // }); 
    // on scroll header bg change
    $(document).on("scroll", function () {
        if ($(this).scrollTop() > 60) {
            $(".header").addClass("change-bg");
        } else {
            $(".header").removeClass("change-bg");
        }
    });
    $('.navbar-toggler').on('click', function () {
        $(".navbar").toggleClass("change-bg");
        $(".navbar-collapse").slideDown(); 
    });

    // back to top button 
    var btn = $('#button');

    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, '100');
    });

    // smooth scroll
    $(function () {
        $('a[href*=\\#]:not([href=\\#])').on('click', function () {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.substr(1) + ']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 600);
                return false;
            }
        });
    });

});