jQuery(document).ready(function () {

    var $ = jQuery;

    var $input = $('#input');
    var $output = $('#output');
    var $channel = $('#channel');
    var $title = $('#title');
    var $channel_list = $('#channel-list');
    var $user_list = $('#user-list');
    var $send_button = $('#send-button');
    var $channel_button = $('#channel-button');
    var $user_button = $('#user-button');
    var $option_button = $('#option-button');
    var $info_button = $('#info-button');
    var $logout_button = $('#logout-button');

    $input.focus();

    $input.keyup(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            read();
        }
    });

    $send_button.click(function () {
        read();
    });

    $channel_button.click(function () {
        setChannel('#cerberbot');
        // TODO
    });

    $user_button.click(function () {
        // TODO
    });

    $option_button.click(function () {
        // TODO
    });

    $info_button.click(function () {
        // TODO
    });

    $logout_button.click(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'logout'
            }
        });
    });

    function read() {
        var input = $input.val();
        if (input == '') {
            return false;
        }
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'read',
                text: input
            }
        });
        $input.val('');
    }

    function write(text) {
        $output.append('<p>' + text + '</p>');
        scroll();
    }

    function scroll() {
        $('.output').stop().animate({
            scrollTop: $('.scrollto')[0].offsetTop
        }, 1000);
    }

    function setChannel(channel) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'setchannel',
                channel: channel
            }
        });
        $output.find('p').remove();
    }

    setInterval(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getoutput'
            }
        }).done(function (json) {
            if (json !== null) {
                if (json.loggedin === false) {
                    location.href = 'login.php';
                } else {
                    $.each(json, function (index, data) {
                        write('[' + data.time + '] &lt;' + data.name + '&gt; ' + data.text);
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
