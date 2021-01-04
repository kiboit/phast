<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    var events = [];

    setTimeout(function () {
        events.push('setTimeout');
    });

    document.addEventListener('DOMContentLoaded', function () {
        events.push('DOMContentLoaded');

        setTimeout(function () {
            next(10);
        });

        function next(depth) {
            events.push('setTimeout via DOMContentLoaded');
            if (depth > 1) {
                setTimeout(function () {
                    next(depth - 1);
                });
            }
        }
    });

    addEventListener('load', function () {
        events.push('load');
    });
</script>
</body>
</html>
