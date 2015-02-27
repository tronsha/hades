<?php

chdir(__DIR__);

require_once('../vendor/autoload.php');

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
}elseif (isset($_POST['action']) && $_POST['action'] == 'logout') {
    echo $hades->logout();
} else {
    echo json_encode(NULL);
}
