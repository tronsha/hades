<?php

chdir(__DIR__);

require_once('../vendor/autoload.php');

use Hades\Hades;

$hades = new Hades;

if ($hades->isLoggedin() === false) {
    echo json_encode(array('loggedin' => false));
} elseif (isset($_POST['action']) && $_POST['action'] == 'pull') {
    echo $hades->pull();
} elseif (isset($_POST['action']) && $_POST['action'] == 'push') {
    echo $hades->push();
} elseif (isset($_POST['action']) && $_POST['action'] == 'logout') {
    echo $hades->logout();
} else {
    echo json_encode(NULL);
}
