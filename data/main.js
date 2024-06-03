$(document).ready(function() {
    console.log("main.js: loaded");

    // navigation burger
    $(".menu-closed").click(function(){
        $(".menu-closed").fadeOut(0);
        $(".menu-open").fadeIn(0);

        $("header nav .mobile-menu").addClass("show");
    });

    $(".menu-open").click(function(){
        $(".menu-open").fadeOut(0);
        $(".menu-closed").fadeIn(0);

        $("header nav .mobile-menu").removeClass("show");
    });

    // alert box triggers
    $("body").on("click", ".alert-box span", function(){
        $(".alert-box").fadeOut();
    });

    // user upload image triggers
    $('#change-avatar input[type="file"]').change(function() {
        $('#change-avatar input[type="submit"]').click();
    });
    $(".change-avatar-trigger").click(function(){
        $('#change-avatar input[type="file"]').click();
    });

    // challenge countdown
    if ($("#countdown").length) {
        var countDownDate = 1000 * $("#countdown").data("end");
        console.log(countDownDate);

        var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $("#days").html("<span>"+days+"</span> Days");
            $("#hours").html("<span>"+hours+"</span> Hours");
            $("#minutes").html("<span>"+minutes+"</span> Minutes");
            $("#seconds").html("<span>"+seconds+"</span> Seconds");

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                $("#countdown").html("<div>This challenge has ended.</div>");
            } else $("#add-challenge-button").fadeIn(0);
        }, 1000);
    }

    // settings tabs
    $(".profile-menu li").click(function(){
        var show = $(this).data("show");
        
        $(".settings form").fadeOut(0);
        $("#"+show).fadeIn();
    });

    // rat training
    $("#feed-rat").click(function(e){
        // disable double tap
        if ($("#feed-rat").data("disabled") != true) {
            $("#feed-rat").data('disabled', true).html('<div class="loader"></div>');

            // show floating and fading number
            x = e.pageX;
            y = e.pageY - 40;
            amount = $("#feed-amount").val();
            if (amount<1 || isNaN(amount)) {
                amount = 1;
                $("#feed-amount").val(1);
            }
            id = $("#feed-amount").data("id");
            
            $.ajax({
                method: "POST",
                url: "/data/parts/action-feed-rat.php",
                data: { rat: id, feed: amount }
            }).done(function(msg) {
                msg = JSON.parse(msg);
                console.log(msg);

                if (msg.error) {
                    $('<div class="form-errors alert-box">'+msg.error+' <span>x</span></div>').appendTo('body');
                } else {
                    $(".level span").html("Level "+msg.rat.lvl);
                    $('#img image').attr('xlink:href', '/data/images/site/rats/'+msg.rat.lvl+'.gif')
                    $(".coins span").html(msg.trainer.coins);
    
                    $('<div class="feed-floating-number">+'+msg.rat.xpdelta+' xp</div>').appendTo('.profile-stats').css({top: y, left: x}).fadeIn(0).animate({top:y-100}, 700, function(){
                        $(this).fadeOut(0).remove();
                    });
        
                    // increase xp bar
                    $(".level .bar").width(msg.rat.xppercent + "%");
                }
        
                $("#feed-rat").data('disabled', false).html('Feed <img src="/data/images/site/cheese.png" alt="Cheese" title="Cheese">');
            });
        }
    });
});