<?php
require __DIR__ . '/../../build/phast.php';
\Kibo\Phast\PhastDocumentFilters::deploy(require __DIR__ . '/test-config.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/stylesheet_large.css">
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css?family=VT323" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">
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
        <script src="https://cdn.jsdelivr.net/npm/retinajs@2.1.3/dist/retina.min.js"></script>
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

        <div class="documentWrite">This should have a blue background</div>
        <script>
            document.write("<style>.documentWrite{background:blue;}</style>");
        </script>

        <div style="width: 200px; height: 200px; background: url(images/python.png)"></div>

        <img src="wow.jpg" width="100" height="100">
        <img src="images/../images/basset.png" data-rjs=2>
        <img src="images/basset.png" width="130" height="155">
        <img src="images/batman.jpg" data-rjs=2>
        <img src="images/batman.jpg" width="84" height="50">
        <img src="https://www.commitstrip.com/wp-content/uploads/2017/09/Strip-Lenfance-du-codeur-Le-piratage-650-finalenglish.jpg">
        <script>
            document.querySelectorAll('img').forEach(function (i) {
                i.removeAttribute('width');
                i.removeAttribute('height');
            });
        </script>

        <iframe src="http://www.businesscat.happyjar.com/wp-content/uploads/2015/04/2015-04-24-Bonding.png" width="1200" height="600"></iframe>
    </body>
</html>
