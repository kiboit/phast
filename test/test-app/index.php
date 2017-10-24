<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$config = require_once __DIR__ . '/../../src/config-example.php';
\Kibo\Phast\PhastDocumentFilters::deploy($config);
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/main.js"></script>
    </head>
    <body>
        <script>
            console.log('Loaded another one!');
        </script>
        <div>This is where we are!</div>
    </body>
</html>
