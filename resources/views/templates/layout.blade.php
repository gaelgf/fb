<?php
session_start();

require_once "fb_sdk/src/Facebook/autoload.php";

$fb = new Facebook\Facebook([
'app_id' => '1545660598783922',
'app_secret' => 'ca702576b5815fbccba65343c8003198',
'default_graph_version' => 'v2.5',
]);

$helper = $fb->getRedirectLoginHelper();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Titre</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/selection_album.js"></script>
    </head>
    <body>
        <header>
            <img src="img/campagnes/1/logo.png" alt="">
            - Concours photos
        </header>


        @yield("content");


    </body>
</html>