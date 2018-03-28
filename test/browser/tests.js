function test(file, fn, withPhast) {
    var name = file.replace(/\.php$/, '').replace(/_/g, ' ');

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
        iframe.contentWindow.addEventListener('error', function (e) {
            console.log(e);
            error++;
        });

        iframe.contentWindow.assert = assert;

        function onFrameLoad() {
            iframe.removeEventListener('load', onFrameLoad);

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

            // Remove this workaround if/when the Browserstack issue gets fixed.
            if (navigator.userAgent === "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/604.5.6 (KHTML, like Gecko) Version/11.0.3 Safari/604.5.6") {
                assert.equal(error, 1, "On High Sierra/Safari 11 on Browserstack, exactly one error should be thrown by Firebug");
            } else {
                assert.equal(error, 0, "No errors should be thrown");
            }

            fn(assert, doc);
        }
    });
}

function async(assert, fn) {
    return fn(assert.async());
}

function retrieve(url, fn, always) {
    var req = new XMLHttpRequest();
    req.open('GET', url);
    req.overrideMimeType('text/plain; charset=x-user-defined');
    req.send();

    req.addEventListener('load', load);
    if (always) {
        req.addEventListener('loadend', always);
    }
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

function loadPhastJS(urls, done) {
    QUnit.config.autostart = false;
    loadPhastJS.awaitingScriptsCount++;

    var loaded = [];
    var loadedCnt = 0;
    urls.forEach(function (url, idx) {
        retrieve(url, function (responseText) {
            loaded[idx] = responseText;
            loadedCnt++;
            if (loadedCnt === urls.length) {
                finish();
            }
        });
    });

    function finish() {
        var phast = {
            scripts: []
        };
        loaded.forEach(function (script) {
            eval(script);
        });
        done(phast);
        loadPhastJS.awaitingScriptsCount--;
        if (!loadPhastJS.awaitingScriptsCount) {
            QUnit.start();
        }
    }
}

function _loadPhastJS(url, done) {

    QUnit.config.autostart = false;
    loadPhastJS.awaitingScriptsCount++;

    retrieve(
        url,
        function (responseText) {
            var phast = {
                scripts: []
            };
            var document = {
                addEventListener: function () {}
            };
            (function (done) {
                eval(responseText);
            })();
            done(phast);
        },
        function () {
            loadPhastJS.awaitingScriptsCount--;
            if (!loadPhastJS.awaitingScriptsCount) {
                QUnit.start();
            }
        });
}
loadPhastJS.awaitingScriptsCount = 0;
