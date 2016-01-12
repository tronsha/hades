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

    var history = [];
    var historyCount = 0;
    var historyPos = 0;

    $(document).keyup(function (event) {
        if (event.keyCode == 27) {
            $infobox.fadeOut(500, function () {
                $overlay.fadeOut(500, function () {
                    $input.focus();
                });
            });
        }
    });

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
                    $input.focus();
                },
                'Cancel': function() {
                    $(this).dialog('close');
                    $input.focus();
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
                    $input.focus();
                },
                'Cancel': function() {
                    $(this).dialog('close');
                    $input.focus();
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
        if (event.which == 13) { /* enter */
            event.preventDefault();
            read();
        } else if (event.keyCode == 38) { /* up arrow */
            if (historyPos < historyCount) {
                historyPos++;
                $input.val(history[historyCount - historyPos]);
            }
        } else if (event.keyCode == 40) { /* down arrow */
            if (historyPos > 0) {
                historyPos--;
                $input.val(history[historyCount - historyPos]);
            }
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
                    $infobox.fadeOut(500, function () {
                        $overlay.fadeOut(500, function () {
                            $input.focus();
                        });
                    });
                })
            }
        });
        $overlay.fadeIn(500, function () {
            $infobox.fadeIn(500);
        });
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
        $overlay.fadeIn(500, function () {
            $infobox.fadeIn(500);
        });
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
        logout();
    });

    /**
     *
     */
    function logout() {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'logout'
            }
        });
    }

    /**
     *
     */
    $overlay.click(function () {
        $infobox.fadeOut(500, function () {
            $overlay.fadeOut(500, function () {
                $input.focus();
            });
        });
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
        history[historyCount] = input;
        historyCount++;
        historyPos = 0;
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
                response(json);
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
                response(json);
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
                    if (json[0].topic !== undefined) {
                        $topic.html(json[0].topic);
                    } else {
                        $topic.html('');
                    }
                }
            });
        }
    }

    /**
     *
     * @param json
     */
    function response(json) {
        if (json.action === 'join') {
            var channel = json.channel;
            setChannel(channel);
            $title.text(channel + ' - Hades');
            $channel.text(channel);
            $topic.text('');
        }
        if (json.action === 'logout') {
            logout();
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
                        var output = '[<span class="time" title="' + dateObject.toLocaleTimeString() + ' / ' + dateObject.toLocaleDateString() + '">' + dateObject.toLocaleTimeString() + '</span>] ';
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
                $overlay.fadeIn(500, function () {
                    $infobox.fadeIn(500);
                });
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

    /**
     *
     */
    $(window).bind('beforeunload', function () {
        return '';
    });

});

var autoscroll = true;
