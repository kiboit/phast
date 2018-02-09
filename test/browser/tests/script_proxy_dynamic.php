<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    (function () {
        var s = document.createElement('script');
        s.src = '../res/script_proxy.js';
        var before = document.getElementsByTagName('script')[0];
        before.parentNode.insertBefore(s, before);
    })();
</script>
</body>
</html>
