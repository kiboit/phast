<?php
if (!empty($_GET['sleep'])) {
    usleep($_GET['sleep'] * 1000);
}
header('Content-Type: text/javascript');
?>
order = window.order || [];
order.push(<?= json_encode($_GET['name']); ?>);
