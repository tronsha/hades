<?php

/*
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

chdir(__DIR__);

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../cerberus/vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require_once($file);
        break;
    }
}

use Hades\Hades;

$hades = new Hades;

if ($hades->isLoggedin() === false) {
    header('Location: login.php');
    exit;
}

$_SESSION['bot'] = $hades->getBotId();
$_SESSION['last'] = 0;
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($_SESSION['channel']) ? $_SESSION['channel'] . ' - Hades' : 'Chat - Hades'; ?></title>
    <meta name="description" content="Hades, master of Cerberus">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/normalize.css" rel="stylesheet" type="text/css">
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css">
    <link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="theme-default">
<div class="chat">
    <h1>Hades</h1>
    <div class="title">
        <span id="channel"><?php echo isset($_SESSION['channel']) ? $_SESSION['channel'] : ''; ?></span>
        <span id="topic"></span>
    </div>
    <div class="output">
        <div id="output"></div>
        <div class="scrollto"></div>
    </div>
    <div class="input">
        <input id="input" placeholder="$">
    </div>
    <div class="buttons">
        <span id="channel-button" class="fa fa-comment" title="channel"></span>
        <span id="whisper-button" class="fa fa-comments" title="whisper"></span>
        <span id="user-button" class="fa fa-users" title="user"></span>
        <span id="media-button" class="fa fa-folder-open" title="media"></span>
        <span id="option-button" class="fa fa-cogs" title="options"></span>
        <span id="info-button" class="fa fa-info" title="info"></span>
        <span id="logout-button" class="fa fa-sign-out" title="logout"></span>
    </div>
    <div class="submit">
        <span id="send-button" class="fa fa-send" title="submit"></span>
    </div>
</div>
<div id="overlay" class="overlay"></div>
<div id="infobox" class="infobox"></div>
<div id="optionbox" class="optionbox">
    <div><h2>Options</h2></div>
    <fieldset>
        <legend>Theme</legend>
        <ul>
            <li><input id="theme-default" name="theme" type="radio" value="default" checked><label for="theme-default">Default Theme</label></li>
            <li><input id="theme-dark" name="theme" type="radio" value="dark"><label for="theme-dark">Dark Theme</label></li>
            <li><input id="theme-light" name="theme" type="radio" value="light"><label for="theme-light">Light Theme</label></li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Autoscroll</legend>
        <ul>
            <li><input id="autoscroll-enable" name="autoscroll" type="radio" value="true" checked><label for="autoscroll-enable">Enable</label></li>
            <li><input id="autoscroll-disable" name="autoscroll" type="radio" value="false"><label for="autoscroll-disable">Disable</label></li>
        </ul>
    </fieldset>
</div>
<div id="dialog"></div>
<script src="js/jquery.js" type="application/javascript"></script>
<script src="js/jquery-ui.js" type="application/javascript"></script>
<script src="js/chat.js" type="application/javascript"></script>
</body>
</html>
