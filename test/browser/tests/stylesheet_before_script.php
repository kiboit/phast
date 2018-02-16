<?php require '_.php'; ?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="../res/stylesheet_large.css">
</head>
<body>
<h1>Hello, World!</h1>
<script>
    (function () {
        var h1 = document.getElementsByTagName('h1')[0];
        h1.className += 'alert-danger';
        var cs = document.defaultView.getComputedStyle(h1, null);
        var backgroundColor = cs.backgroundColor;

        test = function(assert) {
            assert.equal(backgroundColor, colorCodeToRgb('f2dede'),
                         "The stylesheet should be loaded before the script runs");
        };

        function colorCodeToRgb(code) {
            return 'rgb(' + code.replace(/[a-f0-9]{2}/g, function (m) {
                return parseInt(m, 16) + ', ';
            }).replace(/, $/, '') + ')';
        }
    })();
</script>
</body>
</html>
