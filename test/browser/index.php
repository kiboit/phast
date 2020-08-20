<?php

require_once __DIR__ . '/../../build/phast.php';

$signature = (new Kibo\Phast\Security\ServiceSignatureFactory())
    ->make(Kibo\Phast\Environment\DefaultConfiguration::get());

$bundlerTestParams = [];
foreach ([1, 2, 3] as $i) {
    $file = sprintf(
        'http://%s%s/res/text-%s.txt',
        $_SERVER['HTTP_HOST'],
        dirname($_SERVER['PHP_SELF']),
        $i
    );
    $bundlerTestParams[] = Kibo\Phast\Services\Bundler\ServiceParams::fromArray(['src' => $file])
        ->sign($signature)
        ->serialize();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>Phast Unit Tests</title>
<link rel="stylesheet" href="qunit.css">
<style>
    #qunit-fixture {
        background: white;
        position: fixed;
        width: auto;
        height: auto;
        bottom: 10px;
        right: 10px;
        top: auto;
        left: auto;
    }
    #qunit-fixture iframe {
        border: 1px solid #ccc;
        width: 300px;
        height: 300px;
    }
</style>
</head>
<body>

<?php foreach ($bundlerTestParams as $params):?>
    <div data-phast-params="<?=htmlspecialchars($params)?>"></div>
<?php endforeach;?>
<div id="qunit"></div>
<div id="qunit-fixture"></div>
<script>
    window.phast = {
        scripts: []
    };
</script>
<?php foreach (['es6-promise', 'hash', 'service-url', 'resources-loader', 'scripts-loader'] as $script): ?>
<script>(function () {<?= file_get_contents('public/' . $script . '.js'); ?>})();</script>
<?php endforeach; ?>
<script src="qunit.js"></script>
<script src="tests.js?<?= uniqid(); ?>"></script>
<?php foreach (preg_grep('/\..*\./', glob('tests/*.js'), PREG_GREP_INVERT) as $test): ?>
<script src="<?= $test; ?>"></script>
<?php endforeach; ?>
</body>
</html>
