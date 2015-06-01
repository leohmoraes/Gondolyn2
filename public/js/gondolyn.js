
/*
|--------------------------------------------------------------------------
| Generals
|--------------------------------------------------------------------------
*/

$(function() {
    $(".non-form-btn").bind("click", function(e){
        e.preventDefault();
    });

    $('form').submit(function(){
        $('.loading-overlay').show();
    });

    $('a.slow-link').click(function(){
        $('.loading-overlay').show();
    });

    $("#gondolynLoginPanel").bind("click", function(e) {
        e.preventDefault();
        gondolynModal();
        showLoginPanel();
    });

    $(".gondolyn-modal").bind("click", function(){
        $(".gondolyn-modal").fadeOut();
        $(".gondolyn-login").removeClass("gondolyn-login-animate");
    });

    $(window).resize(function(){
        _setDashboard();
    });
    _setDashboard();
});

/*
|--------------------------------------------------------------------------
| Notifications - Growl Style
|--------------------------------------------------------------------------
*/

function gondolynNotify(message) {
    $(".gondolyn-notification").css("display", "block");

    $(".gondolyn-notify-comment").html(message);
    $(".gondolyn-notification").animate({
        right: "20px",
    });

    $(".gondolyn-notify-closer-icon").click(function(){
        $(".gondolyn-notification").animate({
            right: "-300px"
        },"", function(){
            $(".gondolyn-notification").css("display", "none");
            $(".gondolyn-notify-comment").html("");
        });
    });

    setTimeout(function(){
        $(".gondolyn-notification").animate({
            right: "-300px"
        },"", function(){
            $(".gondolyn-notification").css("display", "none");
            $(".gondolyn-notify-comment").html("");
        });
    }, 8000);
}

/*
|--------------------------------------------------------------------------
| Modal Screen
|--------------------------------------------------------------------------
*/

function gondolynModal() {
    $(".gondolyn-modal").fadeIn();
}

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

function showLoginPanel() {
    $(".gondolyn-login").addClass("gondolyn-login-animate");
}

/*
|--------------------------------------------------------------------------
| Dashboard Panel
|--------------------------------------------------------------------------
*/

function _setDashboard () {
    if ($(window).width() < 768) {
        $('.sidebar').css({
            left: '-'+$(window).width()+'px',
        });

        if ($('.sidebar-menu-btn').length === 0) {
            $(".page-header").prepend('<span class="sidebar-menu-btn fa fa-bars raw-margin-right-16"></span>');
        }

        $('.sidebar-menu-btn').unbind().bind('click', function(){
            $('.overlay').fadeIn()
            $('.sidebar').animate({
                left: 0
            }, 'fast');
        });
        $('.overlay').unbind().bind('click', function(){
            $('.overlay').fadeOut();
            $('.sidebar').animate({
                left: '-'+$(window).width()+'px',
            }, 'fast');
        });
    } else {
        $('.sidebar-menu-btn').remove();
        $('.sidebar').css({
            left: 0
        });
    }
}
