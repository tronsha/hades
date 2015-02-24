jQuery(document).ready(function () {

    var $ = jQuery;
    var $output = $('#output');
    var $input = $('#input');
    var $send = $('#send');
    var $channels = $('#channels');
    var $users = $('#users');
    var $options = $('#options');
    var $info = $('#info');
    var $logout = $('#logout');

    $input.focus();

    $input.keyup(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            send();
        }
    });

    $send.click(function () {
        send();
    });

    $logout.click(function () {
        logout();
    });

    function send() {
        var input = $input.val();
        if (input == '') {
            return false;
        }


        $input.val('');
        $output.append(input + '<br>');
        scroll();
    }

    function logout() {

    }

    function scroll() {
        $('.output').stop().animate({
            scrollTop: $('.scrollto')[0].offsetTop
        }, 1000);
    }

    setInterval(function() {
        scroll();
    }, 1000);

});
