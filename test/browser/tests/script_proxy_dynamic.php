<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    var index = 0;

    insert(function (s) {
        s.src = '../res/script_proxy.js';
    });

    insert(function (s) {
        s.src = '../res/script_proxy.js';
        s.type = 'text/javascript';
    });

    insert(function (s) {
        s.src = 'script_proxy_dynamic.module.js';
        s.type = 'module';
    });

    function insert(fn) {
        var s = document.createElement('script');
        s.dataset.testIndex = ++index;
        fn(s);
        var before = document.getElementsByTagName('script')[0];
        before.parentNode.insertBefore(s, before);
    }
</script>
</body>
</html>
