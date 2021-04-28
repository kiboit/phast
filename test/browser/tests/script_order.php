<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<script async src="script_order.script.php?name=async+external&sleep=200&<?= uniqid(); ?>"></script>
<script defer src="script_order.defer.js"></script>
<script defer src="script_order.script.php?name=deferred+external&<?= uniqid(); ?>"></script>
<script>
    order = window.order || [];
    order.push('inline');
</script>
<script defer>
    order = window.order || [];
    order.push('deferred inline');
</script>
<script src="script_order.script.php?name=synchronous+external&<?= uniqid(); ?>"></script>
<script>
    order = window.order || [];
    order.push('second inline');
</script>
</body>
</html>
