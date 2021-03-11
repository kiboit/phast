<?php require '_.php'; ?>
<!doctype html>
<html>
<body>
<?php for ($i = 0; $i < 10; $i++): ?>
<script src="currentscript_async.js.php?i=<?= $i ?>&t=phast"
        data-value="phast"
        data-phast-debug-force-method="SyncBrowserScript"
></script>
<?php endfor; ?>
<?php for ($i = 0; $i < 10; $i++): ?>
<script src="currentscript_async.js.php?i=<?= $i; ?>&t=async"
        data-value="async"
        data-phast-no-defer
        async
></script>
<?php endfor; ?>
</body>
</html>
