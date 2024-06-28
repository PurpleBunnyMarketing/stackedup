$(document).ready(function () {
    // sidebar toggle
    var $sidetoggle = $("body");
    $('.navbar-toggler').click(function () {
        $sidetoggle.toggleClass("menu-open");
    });
    $('.navbar-nav .nav-link').click(function () {
        $('body').removeClass('menu-open');
    }); 
    //stars
    var attr_val = $('#stars li').is('[disabled]');
    if (attr_val != 'disabled') {
        $('#stars li').on('mouseover', function () {
            var onStar = parseInt($(this).data('value'), 10);
            $(this).parent().children('li.star').each(function (e) {
                if (e < onStar) {
                    $(this).addClass('hover');
                }
                else {
                    $(this).removeClass('hover');
                }
            });

        }).on('mouseout', function () {
            $(this).parent().children('li.star').each(function (e) {
                $(this).removeClass('hover');
            });
        });

        $('#stars li').on('click', function () {
            var onStar = parseInt($(this).data('value'), 10);
            var stars = $(this).parent().children('li.star');

            for (i = 0; i < stars.length; i++) {
                $(stars[i]).removeClass('selected');
            }

            for (i = 0; i < onStar; i++) {
                $(stars[i]).addClass('selected');
            }

        });
    }
});