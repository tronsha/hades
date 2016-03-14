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
} elseif (isset($_POST['action']) && $_POST['action'] === 'getoutput') {
    echo $hades->getOutput();
} elseif (isset($_POST['action']) && $_POST['action'] === 'setinput') {
    echo $hades->useInput($_POST['text']);
} elseif (isset($_POST['action']) && $_POST['action'] === 'setchannel') {
    $hades->setChannel($_POST['channel']);
} elseif (isset($_POST['action']) && $_POST['action'] === 'gettopic' && isset($_POST['channel'])) {
    echo $hades->getChannel($_POST['channel']);
} elseif (isset($_POST['action']) && $_POST['action'] === 'getchannel') {
    echo $hades->getChannel();
} elseif (isset($_POST['action']) && $_POST['action'] === 'getwhisper') {
    echo $hades->getWhisper();
} elseif (isset($_POST['action']) && $_POST['action'] === 'getuser') {
    echo $hades->getUser();
} elseif (isset($_POST['action']) && $_POST['action'] === 'getstatus') {
    echo $hades->getStatus();
} elseif (isset($_POST['action']) && $_POST['action'] === 'isrunning') {
    echo $hades->isRunning();
} elseif (isset($_POST['action']) && $_POST['action'] === 'logout') {
    echo $hades->logout();
} else {
    echo json_encode(null);
}
