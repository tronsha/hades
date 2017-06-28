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

chdir(__DIR__);

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../cerberus/vendor/autoload.php'] as $file) {
    if (true === file_exists($file)) {
        require_once $file;
        break;
    }
}

use Hades\Hades;

$hades = new Hades;

if (true === $hades->isLoggedin()) {
    header('Location: index.php');
    exit;
}

if (true === isset($_POST['username']) && true === isset($_POST['password'])) {
    $hades->login($_POST['username'], $_POST['password']);
}
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - Hades</title>
    <meta name="description" content="Hades, master of Cerberus">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1><?php echo $hades->__('login'); ?></h1>
    <div>
        <form action="./login.php" method="post">
            <input name="username" placeholder="<?php echo $hades->__('username'); ?>" type="text">
            <input name="password" placeholder="<?php echo $hades->__('password'); ?>" type="password">
            <input type="submit" value="<?php echo $hades->__('submit'); ?>">
        </form>
    </div>
</div>
</body>
</html>
