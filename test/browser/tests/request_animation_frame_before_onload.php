<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    var events = [];

    window.requestAnimationFrame(function () {
        events.push('requestAnimationFrame');
    });

    document.addEventListener('DOMContentLoaded', function () {
        events.push('DOMContentLoaded');

        window.requestAnimationFrame(function () {
            events.push('requestAnimationFrame via DOMContentLoaded');
        });
    });

    window.addEventListener('load', function () {
        events.push('load');
    });
</script>
</body>
</html>
