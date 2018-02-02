<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>Phast Unit Tests</title>
<link rel="stylesheet" href="qunit.css">
</head>
<body>
<div id="qunit"></div>
<div id="qunit-fixture"></div>
<script src="qunit.js"></script>
<script src="tests.js"></script>
<?php foreach (preg_grep('/\..*\./', glob('tests/*.js'), PREG_GREP_INVERT) as $test): ?>
<script src="<?= $test; ?>"></script>
<?php endforeach; ?>
</body>
</html>
