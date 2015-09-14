jQuery(document).ready(function () {

    var $ = jQuery;

    var $title = $('title');
    var $input = $('#input');
    var $output = $('#output');
    var $channel = $('#channel');
    var $topic = $('#topic');
    var $sendButton = $('#send-button');
    var $channelButton = $('#channel-button');
    var $userButton = $('#user-button');
    var $optionButton = $('#option-button');
    var $infoButton = $('#info-button');
    var $logoutButton = $('#logout-button');
    var $overlay = $('#overlay');
    var $infobox = $('#infobox');

    $input.focus();

    /**
     *
     */
    $('body').on('click', '#output .link', function() {
        var webLink = $(this).text();
        $("#dialog").text(webLink);
        $('#dialog').dialog({
            title: 'URL',
            resizable: false,
            buttons: {
                'Open': function() {
                    window.open(webLink);
                    $(this).dialog('close');
                },
                'Cancel': function() {
                    $(this).dialog('close');
                }
            },
            show: {
                effect: "fade",
                duration: 500
            },
            hide: {
                effect: "fade",
                duration: 500
            }
        });
    });

    /**
     *
     */
    $('body').on('click', '#output .channel', function() {
        var channelName = $(this).text();
        $("#dialog").text(channelName);
        $('#dialog').dialog({
            title: 'Channel',
            resizable: false,
            buttons: {
                'Join': function() {
                    joinChannel(channelName);
                    $(this).dialog('close');
                },
                'Cancel': function() {
                    $(this).dialog('close');
                }
            },
            show: {
                effect: "fade",
                duration: 500
            },
            hide: {
                effect: "fade",
                duration: 500
            }
        });
    });

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
    $sendButton.click(function () {
        read();
    });

    /**
     *
     */
    $channelButton.click(function () {
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
                    $title.text(channel + ' - Hades');
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
    $userButton.click(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getuser'
            }
        }).done(function (json) {
            if (json !== null) {
                $infobox.find('div').remove();
                $infobox.append('<div><h2>User at ' + $channel.text() + '</h2></div>');
                $.each(json, function (index, data) {
                    $infobox.append('<div class="whisper">' + data.username + '</div>');
                });
            }
        });
        $overlay.css('display', 'block');
        $infobox.css('display', 'block');
    });

    /**
     *
     */
    $optionButton.click(function () {
        // TODO
    });

    /**
     *
     */
    $infoButton.click(function () {
        // TODO
    });

    /**
     *
     */
    $logoutButton.click(function () {
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
                action: 'setinput',
                text: input
            }
        }).done(function (json) {
            if (json !== null) {
                console.log(json);
            }
        });
        $input.val('');
    }

    /**
     *
     * @param channel
     */
    function joinChannel(channel) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'setinput',
                text: '/join ' + channel
            }
        }).done(function (json) {
            if (json !== null) {
                console.log(json);
            }
        });
    }

    /**
     *
     * @param channel
     */
    function partChannel(channel) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'setinput',
                text: '/part ' + channel
            }
        }).done(function (json) {
            if (json !== null) {
                console.log(json);
            }
        });
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
        if (autoscroll) {
            $('.output').stop().animate({
                scrollTop: $('.scrollto')[0].offsetTop
            }, 1000);
        }
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
                        var output = '[<span class="time" title="' + dateObject.toLocaleTimeString() + ' / ' + dateObject.toLocaleDateString() + '">' + dateObject.toLocaleTimeString()+ '</span>] ';
                        if (data.action == 1) {
                            output += '<span class="action">' + data.name + ' ' + data.text + '</span>';
                        } else {
                            output += '&lt;<span class="user" title="' + data.name + '">' + data.name + '</span>&gt; <span class="text">' + data.text + '</span>';
                        }
                        write(output);
                        scroll();
                    });
                }
            }
        });
    }, 2000);

    /**
     *
     */
    setInterval(function () {
        setTopic($('#channel').text());
    }, 10000);

    /**
     *
     */
    setInterval(function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'isrunning'
            }
        }).done(function (json) {
            console.log(json);
            if (json === false && $infobox.css('display') !== 'block') {
                $infobox.find('div').remove();
                $infobox.append('<div class="info">Chat is disconnected...</div>');
                $overlay.css('display', 'block');
                $infobox.css('display', 'block');
            }
        });
    }, 120000);

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

var autoscroll = true;
