<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    var events = [];

    function say(message) {
        var p = document.createElement('p');
        p.innerText = message;
        document.body.appendChild(p);
    }

    function trackEvent(name) {
        say("event=" + name + " readyState=" + document.readyState);
        events.push({
            event: name,
            readyState: document.readyState
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        trackEvent('DOMContentLoaded');
    });

    document.addEventListener('readystatechange', function () {
        trackEvent('readystatechange');
    });

    window.addEventListener('load', function () {
        trackEvent('load');
    });
</script>
</body>
</html>
