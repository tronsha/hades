jQuery(document).ready(function () {

    var $ = jQuery;

    var $channel = $('#channel');
    var $title = $('#title');
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
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'logout'
            }
        });
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
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'push',
                channel: channel,
                text: input
            }
        });
        $input.val('');
        //output(input);
    }

    function output(text) {
        $output.append('<p>' + text + '</p>');
        scroll();
    }

    function scroll() {
        $('.output').stop().animate({
            scrollTop: $('.scrollto')[0].offsetTop
        }, 1000);
    }

    setInterval(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'pull'
            }
        }).done(function (json) {
            if (json !== null) {
                if (json.loggedin === false) {
                    location.href = 'login.php';
                } else {
                    $.each(json, function (index, data) {
                        output('[' + data.time + '] &lt;' + data.name + '&gt; ' + data.text);
                        scroll();
                    });
                }
            }
        });
    }, 1000);

    $(window).resize(function () {
        scroll();
    });

});
