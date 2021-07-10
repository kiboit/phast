<?php
define('PHAST_CSP_NONCE', 'secret');
require '_.php';
?>
<!doctype html>
<html>
<body>
<script nonce="secret">
if (!window.SCRIPTS) window.SCRIPTS = [];
window.SCRIPTS.push("correct nonce");
</script>
<script nonce="wrong">
if (!window.SCRIPTS) window.SCRIPTS = [];
window.SCRIPTS.push("incorrect nonce");
</script>
<script>
if (!window.SCRIPTS) window.SCRIPTS = [];
window.SCRIPTS.push("no nonce");
</script>
</body>
</html>
