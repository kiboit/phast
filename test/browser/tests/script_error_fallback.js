test('script_error_fallback.php', function (assert, document) {
    var expected = [
        'inline',
        'deferred inline',
        'synchronous external',
        'second inline',
        'deferred external'
    ];
    assert.ok(document.defaultView.doesNotExistSrc, 'window.doesNotExistSrc is defined');
    assert.equal(document.defaultView.doesNotExistSrc, 'does-not-exist.js', 'window.doesNotExistSrc is not correct')
});
