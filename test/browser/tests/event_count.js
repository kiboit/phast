test('event_count.php', function (assert, document) {
    assert.equal(typeof document.defaultView.events, 'object', 'window.events should be defined');
    assert.equal(document.defaultView.events.DOMContentLoaded, 1, 'DOMContentLoaded should have been triggered once');
});
