<?php

chdir(__DIR__);

require_once('../vendor/autoload.php');

use Hades\Hades;

$hades = new Hades;

if ($hades->isLoggedin() === true) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['username']) && isset($_POST['password'])) {
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css" rel="stylesheet" type="text/css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Calligraffitti" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Login</h1>
    <div>
        <form action="./login.php" method="post">
            <input name="username" placeholder="Username" type="text">
            <input name="password" placeholder="Password" type="password">
            <input type="submit" value="Submit">
        </form>
    </div>
</div>
</body>
</html>
