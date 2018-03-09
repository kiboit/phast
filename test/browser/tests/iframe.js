test('iframe.php', function (assert, document) {
    assert.equal(document.defaultView.iframeLoads, 1, "The IFrame should have been loaded once");
    assert.ok(document.defaultView.srcWasChanged, "Initially, the IFrame src should have had an src of about:blank");
});
