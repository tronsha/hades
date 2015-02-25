<?php

chdir(__DIR__);

require_once('../vendor/autoload.php');

use Hades\Hades;

$hades = new Hades;

if (isset($_POST['action']) && $_POST['action'] == 'pull') {
    echo $hades->pull();
} elseif (isset($_POST['action']) && $_POST['action'] == 'push') {
    echo $hades->push();
} else {
    json_encode(NULL);
}
