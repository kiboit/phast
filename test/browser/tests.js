function test(file, fn, withPhast) {
    var name = file.replace(/\.php$/, '');

    if (withPhast === undefined) {
        withPhast = true;
    }

    QUnit.test(name, function (assert) {
        assert.timeout(5000);

        var fixture = document.getElementById('qunit-fixture');

        var done = assert.async();

        var iframe = document.createElement('iframe');
        iframe.src = 'tests/' + file;
        iframe.addEventListener('load', onFrameLoad);

        fixture.appendChild(iframe);

        var error = 0;
        iframe.contentWindow.addEventListener('error', function () {
            error++;
        });

        iframe.contentWindow.assert = assert;

        function onFrameLoad() {
            var complete = 0;
            var interval = 1;

            setTimeout(waitForComplete);

            function waitForComplete() {
                if (iframe.contentWindow
                    && iframe.contentWindow.document.readyState === 'complete'
                ) {
                    complete++;
                } else {
                    complete = 0;
                }
                if (complete >= 20) {
                    setTimeout(runTest);
                } else {
                    setTimeout(waitForComplete, interval);
                }
            }
        }

        function runTest() {
            done();
            iframe.removeEventListener('load', onFrameLoad);

            var doc = iframe.contentWindow.document;

            var scripts = doc.getElementsByTagName('script');
            var logCount = 0;

            Array.prototype.forEach.call(scripts, function (script) {
                if (/Server-side performance metrics/.test(script.textContent)) {
                    logCount++;
                }
            });

            if (withPhast) {
                assert.ok(scripts.length >= 1, "Any document processed by Phast contains at least one script");
                assert.equal(logCount, 1, "Exactly one script should contain Phast's log message");
            } else {
                assert.equal(logCount, 0, "No scripts should contain Phast's log message");
            }

            assert.equal(error, 0, "No errors should be thrown");

            fn(assert, doc);
        }
    });
}

function async(assert, fn) {
    return fn(assert.async());
}

function retrieve(url, fn) {
    var req = new XMLHttpRequest();
    req.addEventListener('load', load);
    req.open('GET', url);
    req.overrideMimeType('text/plain; charset=x-user-defined');
    req.send();
    function load() {
        fn(this.responseText);
    }
}

function wait(assert, predicate, fn) {
    async(assert, function (done) {
        window.setTimeout(check);
        function check() {
            if (predicate()) {
                done();
                if (fn !== undefined) {
                    fn();
                }
            } else {
                window.setTimeout(check);
            }
        }
    });
}
