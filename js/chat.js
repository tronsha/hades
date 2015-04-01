jQuery(document).ready(function () {

    var $ = jQuery;

    var $input = $('#input');
    var $output = $('#output');
    var $channel = $('#channel');
    var $topic = $('#topic');
    var $send_button = $('#send-button');
    var $channel_button = $('#channel-button');
    var $user_button = $('#user-button');
    var $option_button = $('#option-button');
    var $info_button = $('#info-button');
    var $logout_button = $('#logout-button');
    var $overlay = $('#overlay');
    var $infobox = $('#infobox');

    $input.focus();

    /**
     *
     */
    $input.keyup(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            read();
        }
    });

    /**
     *
     */
    $send_button.click(function () {
        read();
    });

    /**
     *
     */
    $channel_button.click(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getchannel'
            }
        }).done(function (json) {
            if (json !== null) {
                $infobox.find('div').remove();
                $infobox.append('<div><h2>Channel</h2></div>');
                $.each(json, function (index, data) {
                    $infobox.append('<div class="join" title="' + data.topic + '">' + data.channel + '</div>');
                });
                $('.join').on('click', function () {
                    var channel = $(this).text();
                    var topic = $(this).attr('title');
                    setChannel(channel);
                    $channel.text(channel);
                    $topic.text(topic);
                    $overlay.css('display', 'none');
                    $infobox.css('display', 'none');

                })
            }
        });
        $overlay.css('display', 'block');
        $infobox.css('display', 'block');
    });

    /**
     *
     */
    $user_button.click(function () {
        // TODO
    });

    /**
     *
     */
    $option_button.click(function () {
        // TODO
    });

    /**
     *
     */
    $info_button.click(function () {
        // TODO
    });

    /**
     *
     */
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

    /**
     *
     */
    $overlay.click(function () {
        $overlay.css('display', 'none');
        $infobox.css('display', 'none');
    });

    /**
     *
     * @returns {boolean}
     */
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

    /**
     *
     * @param text
     */
    function write(text) {
        $output.append('<p>' + text + '</p>');
        scroll();
    }

    /**
     *
     */
    function scroll() {
        $('.output').stop().animate({
            scrollTop: $('.scrollto')[0].offsetTop
        }, 1000);
    }

    /**
     *
     * @param channel
     */
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

    /**
     *
     * @param channel
     */
    function setTopic(channel) {
        if (channel !== '') {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gettopic',
                    channel: channel
                }
            }).done(function (json) {
                if (json !== null) {
                    $topic.html(json[0].topic);
                }
            });
        }
    }

    /**
     *
     */
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
                        var dateObject = new Date(data.time);
                        write('<span class="time" title="' + dateObject.toLocaleTimeString() + ' / ' + dateObject.toLocaleDateString() + '">[' + dateObject.toLocaleTimeString()+ ']</span> &lt;' + data.name + '&gt; ' + data.text);
                        scroll();
                    });
                }
            }
        });
    }, 1000);

    /**
     *
     */
    setInterval(function () {
        setTopic($('#channel').text());
    }, 10000);

    /**
     *
     */
    $(window).resize(function () {
        scroll();
    });

    /**
     *
     */
    setTopic($('#channel').text());

});
