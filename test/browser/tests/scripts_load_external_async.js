test('scripts_load_external_async.php', function (assert, document) {
    var expected = [
        'inline',
        'deferred inline',
        'synchronous external',
        'second inline',
        'deferred external'
    ];
    assert.equal(document.defaultView.jQuerySrc, 'https://code.jquery.com/jquery-3.3.1.slim.min.js', 'jQuery src is correct');
    assert.notOk(document.defaultView.jQueryLoaded, "jQuery was loaded before sync script");
});
