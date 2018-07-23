loadPhastJS(['public/es6-promise.js', 'public/scripts-loader.js'], function (phast) {

    var ScriptsLoader = phast.ScriptsLoader,
        Promise = phast.ES6Promise;

    QUnit.module('ScriptsLoader', function () {

        QUnit.module('Utilities', function (hooks) {

            var utils, testDoc;

            hooks.beforeEach(function () {
                testDoc = document.implementation.createHTMLDocument('');
                utils = new ScriptsLoader.Utilities(testDoc);
            });

            QUnit.test('Test restoreOriginals()', function (assert) {
                var originalSrc = 'the-original-src';
                var originalType = 'the-original-type';
                var s = testDoc.createElement('script');
                s.setAttribute('src', 'the-src');
                s.setAttribute('id', 'the-id');
                s.setAttribute('data-phast-original-src', originalSrc);
                s.setAttribute('data-phast-original-type', originalType);
                s.setAttribute('data-phast-params', 'some-params');
                utils.restoreOriginals(s);

                assert.equal(s.getAttribute('src'), originalSrc, 'src is restored');
                assert.equal(s.getAttribute('type'), originalType, 'type is restored');
                assert.notOk(s.hasAttribute('data-phast-original-src'), 'phast src has been removed');
                assert.notOk(s.hasAttribute('data-phast-original-type'), 'phast type has been removed');
                assert.notOk(s.hasAttribute('data-phast-params'), 'phast params have been removed');
                assert.notOk(s.hasAttribute('params'), 'phast params have not been copied');
                assert.equal(s.getAttribute('id'), 'the-id', 'id is intact');

                s = testDoc.createElement('script');
                s.setAttribute('type', 'phast-script');
                utils.restoreOriginals(s);
                assert.notOk(s.hasAttribute('type'), 'type attribute has been removed when no data-phast-original-type');
            });

            QUnit.test('Test executeString()', function (assert) {
                window.STRING_EXECUTED = false;
                var string = func2string(function  () { window.STRING_EXECUTED = true; });
                utils.executeString(string);
                assert.ok(window.STRING_EXECUTED, 'String was executed');
                delete window.STRING_EXECUTED;
            });

            QUnit.test('Test writeProtectAndExecuteString()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                testDoc.body.appendChild(s1);
                testDoc.body.appendChild(s2);
                var func = function () {
                    document.write('<p>write works</p>');
                    document.writeln('<p>writeln works</p>');
                };
                var checkWrites = getWriteProtectChecker(assert);
                assert.timeout(100);
                var done = assert.async();

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndExecuteString(s1, func2string(func))
                    .then(function () {
                        checkWrites();
                        var paragraphs = testDoc.getElementsByTagName('p');
                        assert.equal(paragraphs.length, 2, 'Paragraphs have been written');
                        assert.equal(paragraphs[0].innerHTML, 'write works', 'paragraph 0 is correct');
                        assert.equal(paragraphs[1].innerHTML, 'writeln works', 'paragraph 1 is correct');
                        assert.ok(s1 === paragraphs[0].previousSibling, 'paragraph 0 is in the correct position');
                        done();
                    });
            });

            QUnit.test('Test writeProtectAndExecuteString() restores write and writeln after exception', function (assert) {
                var s = testDoc.createElement('script');
                var checkWrites = getWriteProtectChecker(assert);
                assert.timeout(100);
                var done = assert.async();
                var script = 'throw "error";';

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndExecuteString(s, script)
                    .finally(function () {
                        checkWrites();
                        done();
                    });
            });

            QUnit.test('Test replaceElement()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                testDoc.body.appendChild(s1);
                Element.prototype.insertBefore = function () {};
                utils.replaceElement(s1, s2);
                delete Element.prototype.insertBefore;
                assert.equal(1, testDoc.body.children.length, 'There is only one body child');
                assert.ok(testDoc.body.children[0] === s2, 'Script has been replaced');
            });

            QUnit.test('Test writeProtectAndReplaceElement()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                var checkWrite = getWriteProtectChecker(assert);
                assert.timeout(200);
                var done = assert.async();
                s1.setAttribute('id', 'writeProtectAndReplaceElementTestScriptToRemove');
                var writeFunc = function () {
                    document.write('<p id="writtenline"></p>');
                };
                s2.setAttribute('src', 'data:text/javascript;base64,' + utoa(func2string(writeFunc)));
                s2.setAttribute('id', 'writeProtectAndReplaceElementReplacementScript');
                document.body.appendChild(s1);

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndReplaceElement(s1, s2)
                    .finally(function () {
                        checkWrite();
                        var replaced = document.getElementById('writeProtectAndReplaceElementTestScriptToRemove');
                        var replacement = document.getElementById('writeProtectAndReplaceElementReplacementScript');
                        var written = document.getElementById('writtenline');

                        assert.ok(replaced === null, 'Target was replaced');
                        assert.ok(replacement === s2, 'Replacement is in place');
                        assert.ok(written, 'Written exists');
                        document.body.removeChild(replacement);
                        document.body.removeChild(written);
                        done();
                    });
            });

            QUnit.test('Test addPreload()', function (assert) {
                utils.addPreload('some-url');
                assert.equal(testDoc.head.children.length, 2, 'A child in the head is present');
                var link = testDoc.head.children[1];
                assert.equal('LINK', link.nodeName, 'The child is a link');
                assert.equal(link.getAttribute('rel'), 'preload', 'The link is a preload');
                assert.equal(link.getAttribute('as'), 'script', 'The link has correct as');
                assert.equal(link.getAttribute('href'), 'some-url', 'The link has correct href');
            });

            function func2string(func) {
                return '(' + func.toString() + ')();';
            }

            function getWriteProtectChecker(assert) {
                var originalWrite = document.write;
                var originalWriteLn = document.writeln;
                return function () {
                    assert.ok(originalWrite === document.write, 'document.write has been restored');
                    assert.ok(originalWriteLn === document.writeln, 'document.writeln has been restored');
                }
            }

            function utoa(str) {
                return btoa(
                    encodeURIComponent(str).replace(
                        /%([0-9A-F]{2})/g,
                        function toSolidBytes(match, p1) {
                            return String.fromCharCode('0x' + p1);
                        }
                    )
                );
            }
        });

        QUnit.module('Scripts', function (hooks) {

            var Scripts = ScriptsLoader.Scripts;

            var fetch = function (element, successful) {
                var params = JSON.parse(element.getAttribute('data-phast-params'));
                utils._pushCall('fetch', params.src);
                return new Promise(function (resolve, reject) {
                    if (successful) {
                        resolve('contents-for-' + params.src);
                    } else {
                        reject('failure-for-' + params.src);
                    }
                });
            };

            var successfulFetch = function (element) {
                return fetch(element, true);
            };

            var failingFetch = function (element) {
                return fetch(element, false);
            };

            var fallback = {

                init: function () {
                    utils._pushCall('fallbackInit');
                },

                execute: function () {
                    utils._pushCall('fallbackExecute');
                    return Promise.resolve('fallback-promise');
                }
            };

            var utils, element;
            hooks.beforeEach(function () {
                element  = document.createElement('script');
                utils = new UtilsMock();
            });

            QUnit.module('InlineScript', function (hooks) {

                var script, whenInitialized;
                hooks.beforeEach(function () {
                    script = new Scripts.InlineScript(utils, element);
                    whenInitialized = script.init();
                });

                QUnit.test('No init execution', function (assert) {
                    assertEmptyInit(assert, whenInitialized);
                });

                QUnit.test('Execute', function (assert) {
                    var done = getAsync(assert);
                    element.innerHTML = ' <!-- stuff here \nconsole.log("works");';
                    script.execute()
                        .then(function () {
                            assertNumberOfCalls(assert, 2);
                            assertRestoredOriginals(assert, 0, element);
                            assertWriteProtectedStringExecution(assert, 1, element, 'console.log("works");');
                            done();
                        });
                });
            });

            QUnit.module('AsyncBrowserScript', function (hooks) {

                var script, whenInitialized;
                hooks.beforeEach(function () {
                    script = new Scripts.AsyncBrowserScript(utils, element);
                    whenInitialized = script.init();
                });

                QUnit.test('Init execution', function (assert) {
                    assertNumberOfCalls(assert, 3);
                    var copy = assertElementCopied(assert, 0, element);
                    assertRestoredOriginals(assert, 1, copy);
                    assertReplacedElement(assert, 2, element, copy);
                    var done = getAsync(assert);
                    whenInitialized.then(function (testText) {
                        assertPromiseText(assert, testText, 'replaceElement');
                        done();
                    });
                });

                QUnit.test('Test execution method is independent from load', function (assert) {
                    var done = getAsync(assert);
                    script.execute()
                        .then(function (testText) {
                            assertPromiseNotFromFunction(assert, testText);
                            done();
                        });
                });

            });

            QUnit.module('SyncBrowserScript', function (hooks) {

                var script, whenInitialized;
                hooks.beforeEach(function () {
                    script = new Scripts.SyncBrowserScript(utils, element);
                    whenInitialized = script.init();
                });

                QUnit.test('Test adding preload on init', function (assert) {
                    var done = getAsync(assert);
                    whenInitialized.then(function () {
                        assertNumberOfCalls(assert, 1);
                        assertPreloadAdded(assert, 0, element.getAttribute('src'));
                        done();
                    });
                });

                QUnit.test('Test execution', function (assert) {
                    var done = getAsync(assert);
                    script.execute()
                        .then(function (testText) {
                            var copy = assertElementCopied(assert, 1, element);
                            assertRestoredOriginals(assert, 2, copy);
                            assertWriteProtectedReplacedElement(assert, 3, element, copy);
                            assertPromiseText(assert, testText, 'writeProtectAndReplaceElement');
                            done();
                        });
                });
            });

            QUnit.module('AsyncAJAXScript', function (hooks) {

                var whenInitialized;
                hooks.beforeEach(function () {
                    whenInitialized = null;
                    element.setAttribute('data-phast-params', '{"src": "proxied-url"}');
                });

                function makeScript(fetch) {
                    var script = new Scripts.AsyncAJAXScript(utils, element, fetch, fallback);
                    whenInitialized = script.init();
                    return script;
                }

                QUnit.test('Test init load execution', function (assert) {
                    var done = getAsync(assert);
                    makeScript(successfulFetch);
                    assertNumberOfCalls(assert, 1);
                    assertCallToFetch(assert, 0);
                    whenInitialized.then(function (testText) {
                        assertNumberOfCalls(assert, 3);
                        assertRestoredOriginals(assert, 1, element);
                        assertStringExecution(assert, 2, 'contents-for-proxied-url');
                        done();
                    });
                });

                QUnit.test('Test execution method is independent from init', function (assert) {
                    var done = getAsync(assert);
                    makeScript(successfulFetch)
                        .execute()
                        .then(function (testText) {
                            assertPromiseNotFromFunction(assert, testText);
                            done();
                        })
                });

                QUnit.test('Test fallback', function (assert) {
                    var done = getAsync(assert);
                    makeScript(failingFetch);
                    whenInitialized.then(function () {
                        assertNumberOfCalls(assert, 2);
                        assertFallbackInitCall(assert, 1);
                        done();
                    });
                });
            });

            QUnit.module('SyncAJAXScript', function (hooks) {

                var whenInitialized;
                hooks.beforeEach(function () {
                    whenInitialized = null;
                    element.setAttribute('data-phast-params', '{"src": "proxied-url"}');
                });

                function makeScript(fetch) {
                    var script = new Scripts.SyncAJAXScript(utils, element, fetch, fallback);
                    whenInitialized = script.init();
                    return script;
                }

                QUnit.test('Test init start loading', function (assert) {
                    var done = getAsync(assert);
                    makeScript(successfulFetch);
                    assertNumberOfCalls(assert, 1);
                    assertCallToFetch(assert, 0);
                    whenInitialized.then(function () {
                        assertNumberOfCalls(assert, 1);
                        done();
                    });
                });

                QUnit.test('Test execution', function (assert) {
                    var done = getAsync(assert);
                    makeScript(successfulFetch)
                        .execute()
                        .then(function () {
                            assertNumberOfCalls(assert, 3);
                            assertRestoredOriginals(assert, 1, element);
                            assertWriteProtectedStringExecution(assert, 2, element, 'contents-for-proxied-url');
                            done();
                        });
                });

                QUnit.test('Test fallback', function (assert) {
                    var done = getAsync(assert);
                    makeScript(failingFetch)
                        .execute()
                        .then(function (testText) {
                            assertNumberOfCalls(assert, 3);
                            assertFallbackInitCall(assert, 1);
                            assertFallbackExecuteCall(assert, 2);
                            assertFallbackPromise(assert, testText);
                            done();
                        });
                });

            });

            function assertEmptyInit(assert, whenInitialized) {
                assertNumberOfCalls(assert, 0);
                var done = getAsync(assert);
                whenInitialized.then(function () {
                    assertNumberOfCalls(assert, 0);
                    done();
                })
            }

            function assertNumberOfCalls(assert, expectedCallsCount) {
                assert.equal(utils._getCalls().length, expectedCallsCount, expectedCallsCount + ' calls have been made');
            }

            function assertStringExecution(assert, idx, executeString) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'executeString', 'String has been executed');
                assert.equal(calls[idx].params, executeString, 'Executed source was correct');
            }

            function assertRestoredOriginals(assert, idx, element) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'restoreOriginals', 'Originals have been restored');
                assert.ok(calls[idx].params === element, 'The originals were restored to the right element');
            }

            function assertElementCopied(assert, idx, element) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'copyElement', 'Element was copied');
                assert.ok(calls[idx].params === element, 'Correct element was copied');
                return calls[idx].return;
            }

            function assertReplacedElement(assert, idx, target, replacement) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'replaceElement', 'Element was replaced');
                assert.ok(calls[idx].target === target, 'Correct element was replaced');
                assert.ok(calls[idx].replacement === replacement, 'Correct element was added');
            }

            function assertWriteProtectedReplacedElement(assert, idx, target, replacement) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'writeProtectAndReplaceElement', 'Element was write protected and replaced');
                assert.ok(calls[idx].target === target, 'Correct element was replaced');
                assert.ok(calls[idx].replacement === replacement, 'Correct element was added');
            }

            function assertWriteProtectedStringExecution(assert, idx, target, execString) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'writeProtectAndExecuteString', 'String was write protected and executed');
                assert.ok(calls[idx].target === target, 'Correct element was protected');
                assert.ok(calls[idx].string === execString, 'Correct string was executed');
            }

            function assertPreloadAdded(assert, idx, url) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'addPreload', 'Preload was added');
                assert.equal(calls[idx].params,  url, 'Correct url was preloaded');
            }

            function assertPromiseText(assert, testText, expectedFunc) {
                assert.equal(testText, expectedFunc + ' promise', 'Correct promise was returned');
            }

            function assertPromiseNotFromFunction(assert, testText) {
                assert.ok(testText === undefined, 'The promise is not from any util.func');
            }

            function assertCallToFetch(assert, idx) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'fetch', 'Fetch was called');
                assert.equal(calls[idx].params, 'proxied-url', 'Correct url was fetched');
            }

            function assertFallbackInitCall(assert, idx) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'fallbackInit', 'Fallback has been called');
            }

            function assertFallbackExecuteCall(assert, idx) {
                var calls = utils._getCalls();
                assert.equal(calls[idx].name, 'fallbackExecute', 'Fallback has been called');
            }

            function assertFallbackPromise(assert, testText) {
                assert.equal(testText, 'fallback-promise', 'This is a fallback promise');
            }

            function getAsync(assert) {
                assert.timeout(100);
                return assert.async();
            }

            function UtilsMock() {

                var calls = [];

                this._pushCall = function (funcName, params) {
                    calls.push({'name': funcName, params: params});
                };

                this._getCalls = function () {
                    return calls;
                };

                this.restoreOriginals = function (element) {
                    this._pushCall('restoreOriginals', element);
                };

                this.copyElement = function (element) {
                    var returnElement = document.createElement('script');
                    calls.push({name: 'copyElement', params: element, return: returnElement});
                    return returnElement;
                };

                this.replaceElement = function (target, replacement) {
                    calls.push({name: 'replaceElement', target: target, replacement: replacement});
                    return Promise.resolve('replaceElement promise');
                };

                this.writeProtectAndReplaceElement = function (target, replacement) {
                    calls.push({name: 'writeProtectAndReplaceElement', target: target, replacement: replacement});
                    return Promise.resolve('writeProtectAndReplaceElement promise');
                };

                this.writeProtectAndExecuteString = function (target, string) {
                    calls.push({name: 'writeProtectAndExecuteString', target: target, string: string});
                    return Promise.resolve('writeProtectAndExecuteString promise');
                };

                this.executeString = function (string) {
                    this._pushCall('executeString', string);
                    return Promise.resolve('executeString promise');
                };

                this.addPreload = function (url) {
                    this._pushCall('addPreload', url);
                }
            }
        });

        QUnit.module('Scripts.Factory', function (hooks) {

            var Scripts = ScriptsLoader.Scripts;

            var fetch = function () {};

            var factory = new Scripts.Factory(document, fetch);

            var element;
            hooks.beforeEach(function () {
                element = document.createElement('script');
            });

            QUnit.test('Test creating inline script', function (assert) {
                var script = factory.makeScriptFromElement(element);
                assert.ok(script instanceof Scripts.InlineScript, 'Instance of InlineScript');
                assertCorrectBuild(assert, script);
            });

            QUnit.test('Test creating AsyncBrowserScript', function (assert) {
                element.setAttribute('src', 'some-src');
                element.setAttribute('async', '');

                var script = factory.makeScriptFromElement(element);
                assert.ok(script instanceof Scripts.AsyncBrowserScript, 'Correct for only src');
                assertCorrectBuild(assert, script);
            });

            QUnit.test('Test create SyncBrowserScript', function (assert) {
                element.setAttribute('src', 'some-src');
                var script = factory.makeScriptFromElement(element);
                assert.ok(script instanceof Scripts.SyncBrowserScript, 'Correct for only src');
                assertCorrectBuild(assert, script);
            });

            QUnit.test('Test create AsyncAJAXScript', function (assert) {
                element.setAttribute('src', 'proxied-src');
                element.setAttribute('data-phast-original-src', 'original-src');
                element.setAttribute('async', '');

                var script = factory.makeScriptFromElement(element);
                assert.ok(script instanceof Scripts.AsyncAJAXScript, 'Correct type');
                assert.ok(script._fallback instanceof Scripts.AsyncBrowserScript, 'Correct fallback');

                assertCorrectBuild(assert, script, true);
                assertCorrectBuild(assert, script._fallback);
            });

            QUnit.test('Test create SyncAJAXScript', function (assert) {
                element.setAttribute('src', 'proxied-src');
                element.setAttribute('data-phast-original-src', 'original-src');

                var script = factory.makeScriptFromElement(element);
                assert.ok(script instanceof Scripts.SyncAJAXScript, 'Correct type');
                assert.ok(script._fallback instanceof Scripts.SyncBrowserScript, 'Correct fallback');

                assertCorrectBuild(assert, script, true);
                assertCorrectBuild(assert, script._fallback);
            });

            function assertCorrectBuild(assert, script, ajax) {
                assert.ok(script._utils._document === document, 'Correct utils document set');
                assert.ok(script._element === element, 'Correct element set');
                if (ajax) {
                    assert.ok(script._fetch === fetch, 'Correct fetch');
                }
            }

        });

        QUnit.module('Test executeScripts()', function () {

            QUnit.test('Init all scripts', function (assert) {
                var init = 0;
                var scripts = [1, 2, 3].map(function () {
                    return {
                        init: function () {
                            init++;
                        },

                        execute: function () {
                            return Promise.resolve();
                        }
                    };
                });
                ScriptsLoader.executeScripts(scripts);
                assert.equal(init, 3, 'All scripts initialized');
            });

            QUnit.test('Execute correctly', function (assert) {
                var order = [];
                var execs = [
                    function () {
                        order.push(0);
                        return Promise.resolve();
                    },
                    function () {
                        order.push(1);
                        return Promise.reject();
                    },
                    function () {
                        return new Promise(function (resolve) {
                            window.setTimeout(function () {
                                order.push(2);
                                resolve();
                            }, 50);
                        });
                    },
                    function () {
                        order.push(3);
                        return Promise.resolve();
                    }
                ];
                var scripts = execs.map(function (exec) {
                    return {init: function () { return Promise.resolve(); }, execute: exec};
                });

                assert.timeout(100);
                var done = assert.async(2);
                window.setTimeout(function () {
                    assert.equal(order.length, 2, 'Correct length in mid execution');
                    assert.equal(order[0], 0, 'Correct item 0');
                    assert.equal(order[1], 1, 'Correct item 1');
                    done();
                }, 25);

                ScriptsLoader.executeScripts(scripts)
                    .then(function () {
                        assert.equal(order.length, 4, 'Correct length in end');
                        assert.equal(order[2], 2, 'Correct item 0');
                        assert.equal(order[3], 3, 'Correct item 1');
                        done();
                    });
            });

            QUnit.test('Resolve on both init and execute', function (assert) {
                var initialized = false,
                    executed = false;
                var script = {
                    init: function () {
                        return new Promise(function (resolve) {
                            window.setTimeout(function () {
                                initialized = true;
                                resolve();
                            }, 30);
                        });
                    },

                    execute: function () {
                        return new Promise(function (resolve) {
                            executed = true;
                            resolve();
                        });
                    }
                };

                assert.timeout(100);
                var done = assert.async();
                ScriptsLoader.executeScripts([script])
                    .then(function () {
                        assert.ok(initialized, 'Was initialized');
                        assert.ok(executed, 'Was executed');
                        done();
                    });
            });

            QUnit.test('Resolve when error in init', function (assert) {
                var script = {
                    init: function () { return Promise.reject(); },
                    execute: function () { return Promise.resolve(); }
                };
                assert.timeout(100);
                assert.expect(0);
                var done = assert.async();
                ScriptsLoader
                    .executeScripts([script])
                    .then(done);
            });

        });

        QUnit.test('Test finding scripts in order', function (assert) {
            var d = document.implementation.createHTMLDocument('');
            function makeElement (attributes, elementName) {
                elementName = elementName || 'script';
                var e = d.createElement(elementName);
                for (var x in attributes) {
                    e.setAttribute(x, attributes[x]);
                }
                d.body.appendChild(e);
                return e;
            }

            makeElement({'non-phast': ''});
            makeElement({});
            makeElement({}, 'p');
            var inlineDeferred = makeElement({'type': 'phast-script', 'defer': ''});
            var deferred = makeElement({'type': 'phast-script', 'defer': '', 'src': 'some-src'});
            var inline = makeElement({'type': 'phast-script'});
            var external = makeElement({'type': 'phast-script', 'async': ''});

            var factory = {
                makeScriptFromElement: function (element) {
                    return {_element: element};
                }
            };

            var scripts = ScriptsLoader.getScriptsInExecutionOrder(d, factory);
            assert.equal(scripts.length, 4, 'Correct number of scripts found');
            assert.ok(scripts[0]._element === inlineDeferred, 'Correct item 0');
            assert.ok(scripts[1]._element === inline, 'Correct item 0');
            assert.ok(scripts[2]._element === external, 'Correct item 1');
            assert.ok(scripts[3]._element === deferred, 'Correct item 2');
        });


    });

});
