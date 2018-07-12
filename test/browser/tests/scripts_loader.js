loadPhastJS(['public/es6-promise.js', 'public/scripts-loader.js'], function (phast) {

    var testDocument,
        Promise = phast.ES6Promise;

    QUnit.module('ScriptsLoader', function (hooks) {

        hooks.beforeEach(function () {
            delete window.PROXIED_WAS_EXECUTED;
            testDocument = document.implementation.createHTMLDocument();
        });

        QUnit.module('ScriptsLoader.ScriptsFactory', function () {

        });

        QUnit.module('ScriptsLoader.ProxiedSyncScript', function () {

            var Script = phast.ScriptsLoader.ProxiedSyncScript;

            QUnit.test('Test recognition of phast-proxied scripts', function (assert) {
                var notProxiedElement,
                    proxiedElement,
                    notProxiedElementWithPhastMark,
                    noSrcElement,
                    fetch = function () {
                        return new Promise(function () {});
                    };

                notProxiedElement = testDocument.createElement('script');
                notProxiedElement.setAttribute('src', 'this-is-not-proxied');

                proxiedElement = testDocument.createElement('script');
                proxiedElement.setAttribute('src', 'to-proxy-service');
                proxiedElement.setAttribute('data-phast-original-src', 'original-src');

                notProxiedElementWithPhastMark = testDocument.createElement('script');
                notProxiedElementWithPhastMark.setAttribute('src', 'original-src');
                notProxiedElementWithPhastMark.setAttribute('data-phast-original-src', 'original-src');

                noSrcElement = testDocument.createElement('script');

                assert.notOk(new Script(notProxiedElement, fetch).isProxied(), 'Not proxied element was reported as such');
                assert.ok(new Script(proxiedElement, fetch).isProxied(), 'Proxied element was reported as such');
                assert.notOk(
                    new Script(notProxiedElementWithPhastMark, fetch).isProxied(),
                    'No phast-mark element was reported as not proxied'
                );
                assert.notOk(new Script(noSrcElement, fetch).isProxied(), 'No src element was reported as not proxied');
            });

            QUnit.test('Test immediate load for phast-proxied scripts', function (assert) {
                assert.timeout(20);
                var testUrl = 'test-url';
                var done = assert.async();
                var fetch = function (url) {
                    assert.equal(url, testUrl, 'Fetched with correct URL');
                    done();
                };
                var script = testDocument.createElement('script');
                script.setAttribute('src', testUrl);
                script.setAttribute('data-phast-original-src', 'the-original');
                new Script(script, fetch);
            });

            QUnit.test('Test no load for non phast-proxied scripts', function (assert) {
                var called = false,
                    script = testDocument.createElement('script'),
                    fetch = function () {
                        called = true;
                    };
                new Script(script, fetch);
                assert.notOk(called, 'Not proxied script was not fetched');
            });

            QUnit.test('Test execution for proxied sync scripts', function (assert) {
                window.PROXIED_WAS_EXECUTED = false;
                var fetch = function () {
                    return new Promise(function (resolve) {
                        window.setTimeout(function () {
                            var toExec = function () {
                                window.PROXIED_WAS_EXECUTED = true;
                            };
                            resolve('(' + toExec.toString() + ')' + '();');
                        }, 20);
                    })
                };
                var scriptElement = testDocument.createElement('script');
                scriptElement.setAttribute('src', 'proxied');
                scriptElement.setAttribute('data-phast-original-src', 'original');
                var script = new Script(scriptElement, fetch);

                assert.timeout(100);
                var done = assert.async();

                assert.notOk(window.PROXIED_WAS_EXECUTED, 'ProxiedSyncScript was not executed before call');
                window.setTimeout(function () {
                    assert.notOk(window.PROXIED_WAS_EXECUTED, 'ProxiedSyncScript was not executed after load');
                    script.execute()
                        .then(function () {
                            assert.ok(window.PROXIED_WAS_EXECUTED, 'ProxiedSyncScript was executed');
                            done();
                        });
                }, 30);
            });



        });

    });

});
