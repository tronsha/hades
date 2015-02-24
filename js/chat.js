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
            input();
        }
    });

    $send.click(function () {
        input();
    });

    $logout.click(function () {
        // TODO
    });

    $options.click(function () {
        // TODO
    });

    $info.click(function () {
        // TODO
    });

    $channels.click(function () {
        // TODO
    });

    $users.click(function () {
        // TODO
    });

    function input() {
        var input = $input.val();
        if (input == '') {
            return false;
        }

        // TODO

        $input.val('');
        output(input);
    }

    function output(text) {
        $output.append(text + '<br>');
        scroll();
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
