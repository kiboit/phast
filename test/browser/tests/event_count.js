test('event_count.php', function (assert, document) {
    assert.equal(typeof document.defaultView.events, 'object', 'window.events should be defined');
    assert.equal(document.defaultView.events.DOMContentLoaded, 1, 'DOMContentLoaded should have been triggered once');
    assert.equal(document.defaultView.events.readyStateComplete, 1, 'readystatechange with readyState complete should have been triggered once via addEventListener');
    assert.equal(document.defaultView.events.readyStateCompleteFn, 1, 'readystatechange with readyState complete should have been triggered once via onreadystatechange');
    assert.equal(document.defaultView.events.load, 1, 'load should have been triggered once via addEventListener');
    assert.equal(document.defaultView.events.loadFn, 1, 'load should have been triggered once via onload');
});
