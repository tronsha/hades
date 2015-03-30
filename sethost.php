<?php

if (md5($_GET['pw']) === 'e10adc3949ba59abbe56e057f20f883e') {
    $content = file_get_contents('../config.ini');
    $host = $_SERVER["REMOTE_ADDR"];
    $content = preg_replace('/host\ \=\ [0-9\.]+/', 'host = ' . $host, $content);
    file_put_contents('../config.ini', $content);
}
