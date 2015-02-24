<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Chat - Hades</title>
    <meta name="description" content="Hades, master of Cerberus">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css" rel="stylesheet" type="text/css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Calligraffitti" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="chat">
    <h1>Hades</h1>
    <div class="title"></div>
    <div class="output">
        <div id="output"></div>
        <div class="scrollto"></div>
    </div>
    <div class="input">
        <input id="input" placeholder="$">
    </div>
    <div id="channels" class="channels list"></div>
    <div id="users" class="users list"></div>
    <div class="buttons">
        <span id="channels" class="fa fa-comment" title="channels"></span>
        &nbsp;
        <span id="users" class="fa fa-users" title="users"></span>
        &nbsp;
        <span id="options" class="fa fa-cogs" title="options"></span>
        &nbsp;
        <span id="info" class="fa fa-info" title="info"></span>
        &nbsp;
        <span id="logout" class="fa fa-sign-out" title="logout"></span>
    </div>
    <div class="submit">
        <span id="send" class="fa fa-send"></span>
    </div>
</div>
<script src="js/chat.js"></script>
</body>
</html>
