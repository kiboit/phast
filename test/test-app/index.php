<?php
define('PHAST_CONFIG_FILE', __DIR__ . '/test-config.php');
require_once __DIR__ . '/../../src/html-filters.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css?family=VT323" rel="stylesheet">
        <script src="js/deferred.js" defer></script>
        <script src="js/main.js" type="application/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" async></script>
        <style>
            body {
                background-image: url(images/python.png);
                background-repeat: no-repeat;
                background-position: top right;
            }
        </style>
    </head>
    <body>
        <script>
            console.log('Inline JS loaded!');
        </script>

        <div class="my-class">This should have a red background</div>
        <div id="addClass">This should have a yellow background</div>

        <script>
        document.getElementById('addClass').className = 'some-other-class';
        </script>

        <img src="wow.jpg" width="100" height="100">
        <img src="images/basset.png">
        <img src="images/basset.png" width="130" height="155">
        <img src="images/batman.jpg">
        <img src="images/batman.jpg" width="84" height="50">
        <img src="https://www.commitstrip.com/wp-content/uploads/2017/09/Strip-Lenfance-du-codeur-Le-piratage-650-finalenglish.jpg">
        <script>
            document.querySelectorAll('img').forEach(function (i) {
                i.removeAttribute('width');
                i.removeAttribute('height');
            });
        </script>

        <iframe src="http://www.businesscat.happyjar.com/?random&nocache=1" width="1200" height="600"></iframe>
    </body>
</html>
