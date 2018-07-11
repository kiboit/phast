<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script async src="https://code.jquery.com/jquery-3.3.1.slim.min.js" id="jquery"></script>
<script>
    try {
        jQueryLoaded = !!jQuery;
    } catch (e) {
        jQueryLoaded = false;
    }
    jQuerySrc = document.getElementById('jquery').getAttribute('src');
</script>
</body>
</html>
