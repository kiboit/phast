<?php
return [
    'servicesUrl' => preg_replace('~^(.*/browser)?/.*~si', '$1/', $_SERVER['PHP_SELF']) . 'phast.php',
];
