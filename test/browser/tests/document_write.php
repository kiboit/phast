<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    document.write('<h1>Hello, ' + 'World!</h1>');
</script>
<div><script>
    document.write('<h2>Hello, ' + 'World!</h2>');
</script></div>
<script>
    document.getElementById('remove').remove();
    document.write('<h3>Hello, World!</h3>');
</script><div id=remove></div>
</body>
</html>
