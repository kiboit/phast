<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<h1>This should be removed by the async call to document.write</h1>
<script>
    callDocumentWrite = function () {
        document.write('This is the result of the async call to document.write');
    }
</script>
</body>
</html>
