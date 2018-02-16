test('spec_dynamic_scripts_delay_onload.php', function (assert, document) {
    wait(assert, function () {
        return typeof document.defaultView.done !== 'undefined';
    });
}, false);
