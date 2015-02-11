
/*
|--------------------------------------------------------------------------
| Generals
|--------------------------------------------------------------------------
*/
$(function() {
    $(".non-form-btn").bind("click", function(e){
        e.preventDefault();
    });
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
}

/*
|--------------------------------------------------------------------------
| Modal Screen
|--------------------------------------------------------------------------
*/

$(function(){
    $(".gondolyn-modal").bind("click", function(){
        $(".gondolyn-modal").fadeOut();
        $(".gondolyn-login").removeClass("gondolyn-login-animate");
    });
});

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

$(function(){
    $("#gondolynLoginPanel").bind("click", function(e) {
        e.preventDefault();
        gondolynModal();
        showLoginPanel();
    });
});
