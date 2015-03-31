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
$hades->setHost($_SERVER["REMOTE_ADDR"]);
