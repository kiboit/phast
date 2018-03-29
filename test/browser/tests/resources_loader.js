loadPhastJS(['public/es6-promise.js', 'public/resources-loader.js'], function (phast) {

    var Promise = phast.ES6Promise.Promise;

    QUnit.module('ResourcesLoader', function (hooks) {

        var client,
            documentParams = [];

        hooks.before(function () {
            Array.prototype.forEach.call(
                window.document.querySelectorAll('[data-phast-params]'),
                function (el) {
                    var params = phast.ResourceLoader.RequestParams.fromString(el.dataset.phastParams);
                    documentParams.push(params);
                }
            );
        });

        hooks.beforeEach(function () {
            client = new phast.ResourceLoader.BundlerServiceClient('phast.php?service=bundler');
        });

        QUnit.module('RequestParams', function () {

            QUnit.test('Check creating from string', function (assert) {
                var string = JSON.stringify({key1: 'value1', key2: 'value2'});
                var params = phast.ResourceLoader.RequestParams.fromString(string);
                assert.equal(params.key1, 'value1', 'key1 is set right');
                assert.equal(params.key2, 'value2', 'key2 is set right');
                assert.notOk(params.isFaulty(), 'It is not faulty');
            });

            QUnit.test('Check bad params', function (assert) {
                var params = phast.ResourceLoader.RequestParams.fromString('invalid json');
                assert.ok(params.isFaulty(), 'It is faulty');
            });
        });

        QUnit.module('BundlerServiceClient', function () {

            QUnit.test('Test fetching files', function (assert) {
                checkFetchingFiles(assert, client);
            });

            QUnit.test('Test fetching from dedicated service file', function (assert) {
                var done = assert.async();
                var client = new phast.ResourceLoader.BundlerServiceClient('bundler.php');
                var request = client.get(documentParams[0]);
                request.then(function (responseText) {
                    assert.equal('text-1-contents', responseText, 'Fetched correctly');
                    done();
                }).catch(function () {
                    assert.ok(false, 'Does not reject');
                    done();
                });
            });

            QUnit.test('Test error on faulty params', function (assert) {
                var done = assert.async();
                var params = phast.ResourceLoader.RequestParams.fromString('invalid json');
                var request = client.get(params);
                request.then(function () {
                    assert.ok(false, 'Does not reject');
                    done();
                }).catch(function () {
                    assert.ok(true, 'Rejects on invalid json');
                    done();
                });
            });

            QUnit.test('Test handling service errors', function (assert) {
                var iterations = 3;
                var done = assert.async(iterations);
                for (var i = 0; i < iterations; i++) {
                    var params = phast.ResourceLoader.RequestParams.fromObject({fail: 'yes'});
                    var request = client.get(params);
                    request.then(function () {
                        assert.ok(false, 'Rejects');
                        done();
                    }).catch(function () {
                        assert.ok(true, 'Rejects');
                        done();
                    });
                }
            });

            QUnit.test('Test handling network errors', function (assert) {
                var iterations = 3;
                var done = assert.async(iterations);
                var client = new phast.ResourceLoader.BundlerServiceClient('does-not-exist');
                for (var i = 0; i < iterations; i++) {
                    var params = phast.ResourceLoader.RequestParams.fromObject({});
                    var request = client.get(params);
                    request.then(function () {
                        assert.ok(false, 'Rejects');
                        done();
                    }).catch(function () {
                        assert.ok(true, 'Rejects');
                        done();
                    });
                }
            });

            QUnit.test('Test ignoring exceptions while dispatching responses', function (assert) {
                var done = assert.async();
                var badParams = {
                    src: 'some-url',
                    token: 'some-token'
                };
                var request1 = client.get(documentParams[0]);
                var request2 = client.get(phast.ResourceLoader.RequestParams.fromObject(badParams));
                var request3 = client.get(documentParams[1]);
                request1.then(function () {
                    throw 'an error';
                });
                request2.catch(function () {
                    throw 'another error';
                });
                request3.then(function () {
                    assert.ok(true, 'Resolves');
                    done();
                });
            });

            QUnit.test('Test ignoring exceptions while dispatching network error', function (assert) {
                var done = assert.async();
                var client = new phast.ResourceLoader.BundlerServiceClient('bad-url');
                var request1 = client.get(documentParams[0]);
                var request2 = client.get(documentParams[1]);
                request1.catch(function () {
                    throw 'an error';
                });
                request2.catch(function () {
                    assert.ok(true, 'Gets executed');
                    done();
                });
            });

            QUnit.test('Test fetching the same resource twice', function (assert) {
                assert.timeout(2000);
                var done = assert.async(2);
                for (var i in [0, 1]) {
                    client.get(documentParams[0])
                        .then(function (responseText) {
                            assert.equal('text-1-contents', responseText, 'Contents are correct');
                            done();
                        })
                        .catch(function (e) {
                            assert.ok(false, 'Does not throw error: ' + e);
                            done();
                        });
                }
            });
        });

        QUnit.module('IndexedDBResourceCache', function (hooks) {

            var Cache = phast.ResourceLoader.IndexedDBResourceCache;
            Cache.cleanupProbability = 0;

            hooks.beforeEach(function () {
                Cache.setDBName('test' + Date.now())
            });

            QUnit.test('Check fetching files with client', function (assert) {
                assert.timeout(5000);
                var done = assert.async(documentParams.length);
                var cache = new Cache(client);
                checkFetchingFiles(assert, cache, done);
            });

            QUnit.test('Check retrieval from cache', function (assert) {
                assert.timeout(5000);
                var done = assert.async(documentParams.length);
                var cache = new Cache(client);
                Promise.all(documentParams.map(function (params) {
                    return cache.get(params);
                })).then(function () {
                    var cache = new Cache({
                        get: function () {
                            throw 'Calling client when should have fetched from cache';
                        }
                    });
                    checkFetchingFiles(assert, cache, done);
                });
            });

            QUnit.test('Check handling missing store when retrieving from', function (assert) {
                assert.timeout(5000);
                var done = assert.async();
                var dbOpenRequest = Cache.openDB(false);
                dbOpenRequest.then(function (db) {
                    db.close();
                    return new Cache(client).get(documentParams[0]);
                }).then(function (responseText) {
                    assert.equal('text-1-contents', responseText);
                    done();
                });
            });

            QUnit.test('Check cache cleanup', function (assert) {
                assert.timeout(20000);
                assert.expect(3);
                var done = assert.async(3);
                var slowClient = {
                    get: function (params) {
                        return new Promise(function (resolve) {
                            setTimeout(function () {
                                resolve('Token: ' + params.token);
                            }, 100);
                        });
                    }
                };
                var dummyClient = {
                    get: function () {
                        return Promise.reject();
                    }
                };

                var t0 = Date.now();
                var caching = new Cache(slowClient);
                caching.get({token: 1})
                    .then(function () {
                        console.log("Got result 1 at", Date.now() - t0);
                        return caching.get({token: 2});
                    })
                    .then(function () {
                        console.log("Got result 2 at", Date.now() - t0);
                        return caching.get({token: 3});
                    })
                    .then(function () {
                        console.log("Got result 3 at", Date.now() - t0);
                        return Cache.maybeCleanup(90, 1);
                    })
                    .then(function () {
                        console.log("Finished cleanup at", Date.now() - t0);
                        var nonCaching = new Cache(dummyClient);
                        addAssert(1, false);
                        addAssert(2, false);
                        addAssert(3, true);
                        function addAssert(n, expectResult) {
                            var message = "Token " + n + " should" + (expectResult ? " NOT" : "") + " be missing";
                            nonCaching.get({token: n}).then(
                                function () {
                                    done();
                                    if (expectResult) {
                                        console.log("Got result for token " + n + "; OK");
                                    } else {
                                        console.error("Got unexpected result for token " + n);
                                    }
                                    assert.ok(expectResult, message);
                                },
                                function () {
                                    done();
                                    if (!expectResult) {
                                        console.log("Got no result for token " + n + "; OK");
                                    } else {
                                        console.error("Got no result for token " + n);
                                    }
                                    assert.ok(!expectResult, message);
                                }
                            );
                        }
                    });
            });

        });

        function checkFetchingFiles(assert, client, done) {
            done = done || assert.async(documentParams.length);
            assert.expect(documentParams.length);
            documentParams.forEach(function (params, idx) {
                var request = client.get(params);
                request.then(function (responseText) {
                    assert.equal('text-' + (idx + 1) + '-contents', responseText, 'Contents are correct');
                    done();
                });
            });
        }

    });


});
