<?php

error_reporting(0);
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
    echo json_encode(['loggedin' => false]);
} elseif (isset($_POST['action']) && $_POST['action'] == 'getoutput') {
    echo $hades->getOutput();
} elseif (isset($_POST['action']) && $_POST['action'] == 'setinput') {
    echo $hades->useInput($_POST['text']);
} elseif (isset($_POST['action']) && $_POST['action'] == 'setchannel') {
    $hades->setChannel($_POST['channel']);
} elseif (isset($_POST['action']) && $_POST['action'] == 'getchannel') {
    echo $hades->getChannel();
} elseif (isset($_POST['action']) && $_POST['action'] == 'getuser') {
    echo $hades->getUser();
} elseif (isset($_POST['action']) && $_POST['action'] == 'gettopic' && isset($_POST['channel'])) {
    echo $hades->getChannel($_POST['channel']);
} elseif (isset($_POST['action']) && $_POST['action'] == 'isrunning') {
    echo $hades->isRunning();
} elseif (isset($_POST['action']) && $_POST['action'] == 'logout') {
    echo $hades->logout();
} else {
    echo json_encode(NULL);
}
