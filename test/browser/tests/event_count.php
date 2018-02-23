<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    var events = {
        DOMContentLoaded: 0,
        readyStateComplete: 0,
        readyStateCompleteFn: 0,
        load: 0,
        loadFn: 0
    };

    function say(message) {
        var p = document.createElement('p');
        p.innerText = message;
        document.body.appendChild(p);
    }

    document.addEventListener('DOMContentLoaded', function () {
        say('DOMContentLoaded');
        events.DOMContentLoaded++;
    });

    document.addEventListener('readystatechange', function () {
        if (document.readyState === 'complete') {
            say('readystatechange with readyState complete');
            events.readyStateComplete++;
        }
    });

    document.onreadystatechange = function() {
        if (document.readyState === 'complete') {
            say('readystatechange with readyState complete (old-school)');
            events.readyStateCompleteFn++;
        }
    };

    window.addEventListener('load', function () {
        say('load');
        events.load++;
    });

    window.onload = function () {
        say('load (old-school)');
        events.loadFn++;
    }
</script>
</body>
</html>
