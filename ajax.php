<?php

chdir(__DIR__);

foreach (array(__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../cerberus/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        require_once($file);
        break;
    }
}

use Hades\Hades;

$hades = new Hades;

if ($hades->isLoggedin() === false) {
    echo json_encode(array('loggedin' => false));
} elseif (isset($_POST['action']) && $_POST['action'] == 'getoutput') {
    echo $hades->getOutput();
} elseif (isset($_POST['action']) && $_POST['action'] == 'setinput') {
    echo $hades->setInput();
} elseif (isset($_POST['action']) && $_POST['action'] == 'setchannel') {
    $hades->setChannel($_POST['channel']);
} elseif (isset($_POST['action']) && $_POST['action'] == 'getchannel') {
    echo $hades->getChannel(isset($_POST['channel']) === true ? $_POST['channel'] : null);
} elseif (isset($_POST['action']) && $_POST['action'] == 'logout') {
    echo $hades->logout();
} else {
    echo json_encode(NULL);
}
