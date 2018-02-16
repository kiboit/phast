<!doctype html>
<html>
<body>
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function () {
            var s = document.createElement('script');
            s.async = false;
            s.defer = false;
            s.src = 'script_order.script.php?name=1&' + (new Date()).getTime();
            document.body.appendChild(s);

            setTimeout(function() {
                var s = document.createElement('script');
                s.async = false;
                s.defer = false;
                s.src = 'script_order.script.php?name=2&sleep=100&' + (new Date()).getTime();
                document.body.appendChild(s);
            });
        });

        window.addEventListener('load', function () {
            assert.equal(typeof window.order, 'object', "Any scripts should be loaded");
            assert.equal(window.order.length, 2, "Two scripts should be loaded");
            assert.equal(window.order[0], '1', "The first script should have name '1'");
            assert.equal(window.order[1], '2', "The second script should have name '2'");
            done = true;
        });
    })();
</script>
</body>
</html>
