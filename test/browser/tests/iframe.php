<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<iframe src="iframe.content.php"></iframe>
<script>
    (function () {
        var iframe = document.getElementsByTagName('iframe').item(0);
        srcWasChanged = iframe.src === 'about:blank';
    })();
</script>
</body>
</html>
