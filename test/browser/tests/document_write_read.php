<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script>
    TEST_RESULT = {}
    document.write('<h1>Hello, World!</h1>');
    TEST_RESULT.FOUND_H1 = document.getElementsByTagName('h1').length == 1;
</script>
</body>
</html>
