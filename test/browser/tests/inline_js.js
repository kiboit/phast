test('inline_js.php', function (assert, document) {
    assert.equal(document.defaultView.didLoad, true, "didLoad was set to true by inline script");
});
