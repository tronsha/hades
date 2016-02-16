<?php

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
<body class="theme-one">
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
    <div style="padding: 20px;">
        <fieldset>
            <legend style="padding: 0 10px;">Theme</legend>
            <input id="theme-one" name="theme" type="radio" value="one" style="margin-right: 10px;" checked><label for="theme-one">one</label>
            <input id="theme-two" name="theme" type="radio" value="two" style="margin-right: 10px;"><label for="theme-two">two</label>
        </fieldset>
        <br>
        <fieldset>
            <legend style="padding: 0 10px;">Autoscroll</legend>
            <input id="autoscroll-enable" name="autoscroll" type="radio" value="true" style="margin-right: 10px;" checked><label for="autoscroll-enable">enable</label>
            <input id="autoscroll-disable" name="autoscroll" type="radio" value="false" style="margin-right: 10px;"><label for="autoscroll-disable">disable</label>
        </fieldset>
    </div>
</div>
<div id="dialog"></div>
<script src="js/jquery.js" type="application/javascript"></script>
<script src="js/jquery-ui.js" type="application/javascript"></script>
<script src="js/chat.js" type="application/javascript"></script>
</body>
</html>
