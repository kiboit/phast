/**
 * IE would trigger a <script> again after replacing the SRC attribute, but only
 * if the TYPE attribute was present.
 */

test('script_once.php', function (assert, document) {
    var done = assert.async();
    setTimeout(function () {
        assert.equal(document.defaultView.COUNT || 0, 1,
                     "The script should have been executed exactly once");
        done();
    }, 100);
});
