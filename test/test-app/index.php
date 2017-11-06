<?php
define('PHAST_CONFIG_FILE', __DIR__ . '/test-config.php');
require_once __DIR__ . '/../../src/html-filters.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css?family=VT323" rel="stylesheet">
        <script src="js/main.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    <body>
        <script>
            console.log('Loaded another one!');
        </script>
        <div>This is where we are!</div>
        <img src="wow.jpg" width="100" height="100">
        <img src="images/basset.png">
        <img src="images/basset.png" width="130" height="155">
        <img src="images/batman.jpg">
        <img src="images/batman.jpg" width="84" height="50">
        <img src="https://www.commitstrip.com/wp-content/uploads/2017/09/Strip-Lenfance-du-codeur-Le-piratage-650-finalenglish.jpg">
        <script>
            document.querySelectorAll('img').forEach(i => { i.removeAttribute('width'); i.removeAttribute('height'); });
        </script>

        <iframe src="http://www.businesscat.happyjar.com/?random&nocache=1" width="1200" height="600"></iframe>
    </body>
</html>
