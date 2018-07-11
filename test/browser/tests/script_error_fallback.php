<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script id="does-not-exist" src="does-not-exist.js"></script>
<script>
    doesNotExistSrc = document.getElementById('does-not-exist').getAttribute('src');
</script>
</body>
</html>
