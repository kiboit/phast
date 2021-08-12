<?php
define('PHAST_CSP_NONCE', 'secret');
require '_.php';
?>
<!doctype html>
<html>
<body>
<script data-phast-no-defer>
window.REPORTS = 0;

window.fetch = function (resource, init) {
    if (/phast-report/.test(resource)) {
        window.REPORTS++;
    }
}
</script>
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
