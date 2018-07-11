<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script async src="https://code.jquery.com/jquery-3.3.1.slim.min.js" id="jquery"></script>
<script>
    function checkJQuery() {
        try {
            return !!jQuery;
        } catch (e) {
            return false;
        }
    }
    jQueryLoaded = checkJQuery();
    var jqueryElement = document.getElementById('jquery');
    jQuerySrc = jqueryElement.getAttribute('src');
    window.addEventListener('load', function () {
        onLoadCalledAfterJQuery = checkJQuery();
    });
</script>
</body>
</html>
