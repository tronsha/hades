<?php

/*
 * Cerberus IRCBot
 * Copyright (C) 2008 - 2017 Stefan Hüsges
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
        require_once $file;
        break;
    }
}

use Hades\Hades;

$hades = new Hades;

if ($hades->isLoggedin() === false) {
    echo json_encode(['loggedin' => false]);
} elseif (true === isset($_POST['action']) && 'getoutput' === $_POST['action']) {
    echo $hades->getOutput();
} elseif (true === isset($_POST['action']) && 'setinput' === $_POST['action']) {
    echo $hades->useInput($_POST['text']);
} elseif (true === isset($_POST['action']) && 'setchannel' === $_POST['action']) {
    $hades->setChannel($_POST['channel']);
} elseif (true === isset($_POST['action']) && 'gettopic' === $_POST['action'] && true === isset($_POST['channel'])) {
    echo $hades->getChannel($_POST['channel']);
} elseif (true === isset($_POST['action']) && 'getchannel' === $_POST['action']) {
    echo $hades->getChannel();
} elseif (true === isset($_POST['action']) && 'getwhisper' === $_POST['action']) {
    echo $hades->getWhisper();
} elseif (true === isset($_POST['action']) && 'getuser' === $_POST['action']) {
    echo $hades->getUser();
} elseif (true === isset($_POST['action']) && 'getstatus' === $_POST['action']) {
    echo $hades->getStatus();
} elseif (true === isset($_POST['action']) && 'getchannellist' === $_POST['action']) {
    echo $hades->getChannellist();
} elseif (true === isset($_POST['action']) && 'isrunning' === $_POST['action']) {
    echo $hades->isRunning();
} elseif (true === isset($_POST['action']) && 'logout' === $_POST['action']) {
    echo $hades->logout();
} else {
    echo json_encode(null);
}
