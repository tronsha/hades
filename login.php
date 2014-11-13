<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - Hades</title>
    <meta name="description" content="Hades, master of Cerberus.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Calligraffitti' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/style.css">
    <style>
        html {
            background: #cccccc;
            font-family: Calligraffitti;
            height: 100%;
            width: 100%;
        }
        .login {
            background: #ffffff;
            border-radius: 10px;
            left: 50%;
            max-width: 100%;
            overflow: hidden;
            position: absolute;
            text-align: center;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 400px;
        }

        .login h1 {
            background: #000000;
            color: #ffffff;
            font-size: 18px;
            margin: 0;
            padding: 6px 0;
            width: 100%;
        }

        .login div {
            background: linear-gradient(#049a9b, #000000);
            padding: 10px 0;
        }

        .login input {
            border-radius: 6px;
            line-height: 20px;
            padding: 6px;
        }

        .login input[type="password"],
        .login input[type="text"] {
            margin: 0 0 10px 0;
            width: 90%;
        }

        .login input[type="submit"] {
            width: 100px;
        }
    </style>
</head>
<body>
<div class="login">
    <h1>Login</h1>
    <div>
        <input type="text" placeholder="Username">
        <input type="password" placeholder="Password">
        <input type="submit" value="Submit">
    </div>
</div>
</body>
</html>
