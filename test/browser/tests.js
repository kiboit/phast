function test(file, fn) {
    var name = file.replace(/\.php$/, '');

    QUnit.test(name, function (assert) {
        var fixture = document.getElementById('qunit-fixture');

        var done = assert.async();

        var iframe = document.createElement('iframe');
        iframe.src = 'tests/' + file;
        iframe.addEventListener('load', onFrameLoad);

        fixture.appendChild(iframe);

        function onFrameLoad() {
            done();
            iframe.removeEventListener('load', onFrameLoad);

            var doc = iframe.contentWindow.document;

            var scripts = doc.getElementsByTagName('script');
            assert.ok(scripts.length >= 1, "Any document processed by Phast contains at least one script");

            var logCount = 0;
            Array.prototype.forEach.call(scripts, function (script) {
                if (/Page automatically optimized/.test(script.textContent)) {
                    logCount++;
                }
            });

            assert.equal(logCount, 1, "Exactly one script should contain Phast's log message");

            fn(assert, doc);
        }
    });
}
