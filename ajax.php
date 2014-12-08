<?php

chdir(__DIR__);
require_once('../vendor/autoload.php');

use \Cerberus\Cerberus;
use \Hades\Db;

$path = Cerberus::getPath();
$config = parse_ini_file($path . '/config.ini', true);

$db = new Db($config['db']);
$db->connect();

