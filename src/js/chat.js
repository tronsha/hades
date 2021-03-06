/*!
 * Cerberus IRCBot
 * Copyright (C) 2008 - 2016 Stefan Hüsges
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses/>.
 */
jQuery(document).ready(function () {

    var $ = jQuery;

    var $title = $('title');
    var $input = $('#input');
    var $output = $('#output');
    var $channel = $('#channel');
    var $topic = $('#topic');
    var $sendButton = $('#send-button');
    var $channelButton = $('#channel-button');
    var $whisperButton = $('#whisper-button');
    var $userButton = $('#user-button');
    var $listButton = $('#list-button');
    var $optionButton = $('#option-button');
    var $infoButton = $('#info-button');
    var $logoutButton = $('#logout-button');
    var $overlay = $('#overlay');
    var $infobox = $('#infobox');
    var $optionbox = $('#optionbox');
    var $connection = $('#connection');

    var history = [];
    var historyCount = 0;
    var historyPos = 0;

    $(document).on('keyup', function (event) {
        if (27 === event.keyCode) {
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
    $('body').on('click', '#output .link', function () {
        var webLink = $(this).text();
        $('#dialog').html('<p></p>');
        $('#dialog p').text(webLink);
        $('#dialog').dialog({
            title: 'URL',
            resizable: false,
            buttons: {
                'Open': function () {
                    window.open(webLink);
                    $(this).dialog('close');
                    $input.focus();
                },
                'Cancel': function () {
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
    $('body').on('click', '#output .channel, .list-container .channel', function () {
        var channelName = $(this).text();
        $('#dialog').html('<p></p>');
        $('#dialog p').text(channelName);
        $('#dialog').dialog({
            title: 'Channel',
            resizable: false,
            buttons: {
                'Join': function () {
                    joinChannel(channelName);
                    $(this).dialog('close');
                    $input.focus();
                },
                'Cancel': function () {
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
    $input.on('keyup', function (event) {
        if (13 === event.which) { /* enter */
            event.preventDefault();
            read();
        } else if (38 === event.keyCode) { /* up arrow */
            if (historyPos < historyCount) {
                historyPos++;
                $input.val(history[historyCount - historyPos]);
            }
        } else if (40 === event.keyCode) { /* down arrow */
            if (historyPos > 0) {
                historyPos--;
                $input.val(history[historyCount - historyPos]);
            }
        }
    });

    /**
     *
     */
    $sendButton.on('click', function () {
        read();
    });

    /**
     *
     */
    $channelButton.on('click', function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getchannel'
            }
        }).done(function (json) {
            if (null !== json) {
                $infobox.find('div').remove();
                $infobox.append('<div><h2>Channel</h2></div>');
                $.each(json, function (index, data) {
                    $infobox.append('<div class="join-container"><div class="join" title="' + data.topic + '">' + data.channel + '</div><span class="close" title="close"><i class="fa fa-close"></i></span></div>');
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
    $('body').on('click', '.box .close', function () {
        event.preventDefault();
        var $this = $(this);
        var $parent = $this.parent('div');
        partChannel($parent.text());
        $parent.remove();
    });

    /**
     *
     */
    $('body').on('click', '.box .whois', function () {
        event.preventDefault();
        var $this = $(this);
        var $parent = $this.parent('div');
        whoisUser($parent.text());
    });

    /**
     *
     */
    $userButton.on('click', function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getuser'
            }
        }).done(function (json) {
            if (null !== json) {
                $infobox.find('div').remove();
                $infobox.append('<div><h2>User at ' + $channel.text() + '</h2></div>');
                $.each(json, function (index, data) {
                    $infobox.append('<div class="whisper-container"><div class="whisper">' + data.username + '</div><span class="whois" title="whois"><i class="fa fa-question"></i></span></div>');
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
    $whisperButton.on('click', function () {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getwhisper'
            }
        }).done(function (json) {
            if (null !== json) {
                $infobox.find('div').remove();
                $infobox.append('<div><h2>Whisper User</h2></div>');
                $.each(json, function (index, data) {
                    $infobox.append('<div class="whisper-container"><div class="whisper">' + data.channel + '</div></div>');
                });
                $('.whisper').on('click', function () {
                    var whisper = $(this).text();
                    setChannel(whisper);
                    $title.text(whisper + ' - Hades');
                    $channel.text(whisper);
                    $topic.text('');
                    $infobox.fadeOut(500, function () {
                        $overlay.fadeOut(500, function () {
                            $input.focus();
                        });
                    });
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
    $optionButton.on('click', function () {
        $overlay.fadeIn(500, function () {
            $optionbox.fadeIn(500);
        });
    });

    $listButton.on('click', function () {
        if ($listButton.hasClass('disabled')) {
            return false;
        }
        if ($listButton.hasClass('fa-file-text')) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'getchannellist'
                }
            }).done(function (json) {
                if (null !== json) {
                    $infobox.find('div').remove();
                    $infobox.append('<div><h2>Channel List</h2></div>');
                    $.each(json, function (index, data) {
                        $infobox.append('<div class="list-container"><div class="channel">' + data.channel + '</div></div>');
                    });
                }
            });
            $overlay.fadeIn(500, function () {
                $infobox.fadeIn(500);
            });
        } else if ($listButton.hasClass('fa-file')) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'setinput',
                    text: '/list'
                }
            });
            $listButton.addClass('disabled');
        }
        return true;
    });

    /**
     *
     */
    $infoButton.on('click', function () {
        // TODO
    });

    /**
     *
     */
    $logoutButton.on('click', function () {
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
    $overlay.on('click', function () {
        var $box = false;
        if ('block' === $infobox.css('display')) {
            $box = $infobox;
        }
        if ('block' === $optionbox.css('display')) {
            $box = $optionbox;
        }
        $box.fadeOut(500, function () {
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
        if ('' === input) {
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
            if (null !== json) {
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
            if (null !== json) {
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
            if (null !== json) {
            }
        });
    }

    /**
     *
     * @param user
     */
    function whoisUser(user) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'setinput',
                text: '/whois ' + user
            }
        }).done(function (json) {
            if (null !== json) {
                response(json);
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
        if ('' !== channel) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gettopic',
                    channel: channel
                }
            }).done(function (json) {
                if (null !== json && undefined !== json[0] && undefined !== json[0].topic) {
                    $topic.html(json[0].topic);
                } else {
                    $topic.html('');
                }

            });
        }
    }

    /**
     *
     * @param json
     */
    function response(json) {
        if ('join' === json.action) {
            var channel = json.channel[0];
            setChannel(channel);
            $title.text(channel + ' - Hades');
            $channel.text(channel);
            $topic.text('');
        }
        if ('logout' === json.action) {
            logout();
        }
        if ('status' === json.type) {
            responseStatus(json);
        }
    }

    /**
     *
     * @param text
     */
    function responseStatus(json) {
        if (undefined === json.status) {
            return false;
        }
        if (323 === parseInt(json.status)) {
            $listButton.removeClass('fa-file');
            $listButton.addClass('fa-file-text');
            $listButton.removeClass('disabled');
            setTimeout(function () {
                $listButton.removeClass('fa-file-text');
                $listButton.addClass('fa-file');
            }, 3600000);
            return null;
        }
        $('#dialog').html('<p></p>');
        $('#dialog p').html(json.text);
        $('#dialog').dialog({
            title: json.status,
            resizable: false,
            buttons: {
                'Ok': function () {
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
        if (470 === parseInt(json.status)) {
            var channel = json.data.forwarding;
            setChannel(channel);
            $title.text(channel + ' - Hades');
            $channel.text(channel);
        }
        return true;
    }

    /**
     *
     * @param text
     */
    function invitedToJoin(json) {
        $('#dialog').html('<p></p>');
        $('#dialog p').text(json.text);
        $('#dialog').dialog({
            title: json.status,
            resizable: false,
            buttons: {
                'Join': function () {
                    joinChannel(json.data.channel);
                    $(this).dialog('close');
                    $input.focus();
                },
                'Ignore': function () {
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
                action: 'getstatus'
            }
        }).done(function (json) {
            if (null !== json && undefined !== json.status) {
                if ('INVITE' === json.status) {
                    invitedToJoin(json);
                } else {
                    responseStatus(json);
                }
            }
        });
    }, 5000);

    /**
     *
     */
    $('body').on('click', '#output .lock', function () {
        var $this = $(this);
        if ($this.hasClass('fa-lock')) {
            $this.removeClass('fa-lock');
            $this.addClass('fa-unlock');
            $this.parent('p').find('.text').css('display', '');
            $this.parent('p').find('.crypt').css('display', 'none');
        } else if ($this.hasClass('fa-unlock')) {
            $this.removeClass('fa-unlock');
            $this.addClass('fa-lock');
            $this.parent('p').find('.text').css('display', 'none');
            $this.parent('p').find('.crypt').css('display', '');
        }
    });

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
            if (null !== json) {
                if (false === json.loggedin) {
                    location.href = 'login.php';
                } else {
                    $.each(json, function (index, data) {
                        var dateObject = new Date(data.time);
                        var output = '[<span class="time" title="' + dateObject.toLocaleTimeString() + ' / ' + dateObject.toLocaleDateString() + '">' + dateObject.toLocaleTimeString() + '</span>] ';
                        if (undefined !== data.crypt) {
                            output += '<i class="lock fa fa-lock"></i> ';
                        }
                        if (1 === parseInt(data.action)) {
                            output += '<span class="action">' + data.name + ' ' + data.text + '</span>';
                        } else {
                            output += '&lt;<span class="user" title="' + data.name + '">' + data.name + '</span>&gt; <span class="text"' + (undefined !== data.crypt ? ' style="display: none;"' : '') + '>' + data.text + '</span>';
                        }
                        if (undefined !== data.crypt) {
                            output += '<span class="crypt">' + data.crypt + '</span>';
                        }
                        write(output);
                        scroll();
                    });
                }
            }
        });
    }, 3000);

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
            if (false === json) {
                $connection.children('.fa-flash').css('display', 'none');
                $connection.children('.fa-refresh').css('display', '');
            } else {
                $connection.children('.fa-flash').css('display', '');
                $connection.children('.fa-refresh').css('display', 'none');
            }
        });
    }, 12000);

    /**
     *
     */
    $connection.on('click', '.fa-refresh', function () {
        location.reload(true);
    });

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
    $(window).on('beforeunload', function () {
        if ('none' === $('.fa-refresh').css('display')) {
            return '';
        }
    });

    /**
     *
     */
    $('body').on('change', '#theme-default, #theme-dark, #theme-light', function () {
        var $this = $(this);
        if (true === $this.prop('checked')) {
            $('body').attr('class', '').addClass('theme-' + $this.attr('value'));
        }
    });

    /**
     *
     */
    $('body').on('change', '#autoscroll-enable, #autoscroll-disable', function () {
        var $this = $(this);
        if (true === $this.prop('checked')) {
            autoscroll = ('true' === $this.attr('value'));
        }
    });

    /**
     *
     */
    $('.input').hover(
        function () {
            var $menu = $('.input > .menu');
            $menu.css('height', 'auto');
            var height = $menu.height() + 20;
            $menu.css('height', '0');
            //$menu.animate({padding: '10px 20px', height: height}, 100);
        },
        function () {
            var $menu = $('.input > .menu');
            //$menu.animate({height: 0, padding: '0px 20px'}, 100);
        }
    );

});

var autoscroll = true;
