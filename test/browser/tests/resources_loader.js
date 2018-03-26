loadPhastJS('public/resources-loader.js', function (phast) {

    QUnit.module('ResourcesLoader', function (hooks) {

        var client,
            documentParams = [];

        hooks.before(function () {
            document.querySelectorAll('[data-phast-params]').forEach(function (el) {
                var params = phast.ResourceLoader.RequestParams.fromString(el.dataset.phastParams);
                documentParams.push(params);
            });
        });

        hooks.beforeEach(function () {
            client = new phast.ResourceLoader.BundlerServiceClient('phast.php?service=bundler');
        });

        QUnit.module('Request', function (hooks) {
            var request;

            hooks.beforeEach(function () {
                request = new phast.ResourceLoader.Request();
            });

            QUnit.test('Check setting/getting handlers', function (assert) {

                assert.strictEqual(request.onsuccess, undefined, 'Initially onsuccess is undefined');
                assert.strictEqual(request.onerror, undefined, 'Initially onerror is undefined');

                var onerror = function () {},
                    onsuccess = function () {};

                request.onsuccess = onsuccess;
                request.onerror = onerror;

                assert.strictEqual(request.onsuccess, onsuccess, 'The onsuccess sets correctly');
                assert.strictEqual(request.onerror, onerror, 'The onerror sets correctly');
            });

            QUnit.test('Check resolving/rejecting', function (assert) {
                var called = false;
                request.onsuccess = function (responseText) {
                    assert.equal('my css', responseText, 'Resolves with appropriate argument');
                    called = true;
                };
                request.success('my css');
                assert.ok(called, 'onsuccess callback was called');
                called = false;

                request = new phast.ResourceLoader.Request();
                request.onerror = function () {
                    called = true;
                };
                request.error();
                assert.ok(called, 'onerror callback was called');
            });

            QUnit.test('Check not dying when resolving with no handlers', function (assert) {
                request.error();
                request.success();
                assert.expect(0);
            });

            QUnit.test('Check instant callback if already resolved/rejected', function (assert) {
                var successCalled = false,
                    errorCalled = false;

                request.success('my js');
                request.onsuccess = function (responseText) {
                    assert.equal('my js', responseText, 'Response text is ok');
                    successCalled = true;
                };
                request.onerror = function () {
                    errorCalled = true;
                };

                assert.ok(successCalled, 'Success callback was called on success');
                assert.notOk(errorCalled, 'Error callback was not called on success');

                successCalled = false;
                errorCalled = false;

                request = new phast.ResourceLoader.Request();
                request.error();
                request.onsuccess = function () {
                    successCalled = true;
                };
                request.onerror = function () {
                    errorCalled = true;
                };

                assert.notOk(successCalled, 'Success was not called on error');
                assert.ok(errorCalled, 'Error was not called on error');

            });

            QUnit.test('Check resolving/rejecting only once', function (assert) {
                assert.expect(0);
                var done = assert.async(2);
                var resolved = new phast.ResourceLoader.Request();
                var rejected = new phast.ResourceLoader.Request();
                resolved.onsuccess = function () {
                    done();
                };
                rejected.onerror = function () {
                    done();
                };
                resolved.success('');
                resolved.success('');
                resolved.error();

                rejected.error();
                rejected.error();
                rejected.success('');
            });

            QUnit.test('Check calling onend on resolving', function (assert) {
                assert.expect(0);
                var done = assert.async();
                request.onsuccess = function () {};
                request.onend = function () {
                    done();
                };
                request.success('');
            });

            QUnit.test('Check calling onend on error', function (assert) {
                assert.expect(0);
                var done = assert.async();
                request.onerror = function() {};
                request.onend = function () {
                    done();
                };
                request.error();
            });

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
                request.onsuccess = function (responseText) {
                    assert.equal('text-1-contents', responseText, 'Fetched correctly');
                    done();
                };
            });

            QUnit.test('Test error on faulty params', function (assert) {
                var params = phast.ResourceLoader.RequestParams.fromString('invalid json');
                var request = client.get(params);
                request.onerror = function () {
                    assert.expect(0);
                };
            });

            QUnit.test('Test handling service errors', function (assert) {
                assert.expect(0);
                var iterations = 3;
                var done = assert.async(iterations);
                for (var i = 0; i < iterations; i++) {
                    var params = phast.ResourceLoader.RequestParams.fromObject({fail: 'yes'});
                    var request = client.get(params);
                    request.onerror = function () {
                        done();
                    };
                }
            });

            QUnit.test('Test handling network errors', function (assert) {
                assert.expect(0);
                var iterations = 3;
                var done = assert.async(iterations);
                var client = new phast.ResourceLoader.BundlerServiceClient('does-not-exist');
                for (var i = 0; i < iterations; i++) {
                    var params = phast.ResourceLoader.RequestParams.fromObject({});
                    var request = client.get(params);
                    request.onerror = function () {
                        done();
                    };
                }
            });

            QUnit.test('Test ignoring exceptions while dispatching responses', function (assert) {
                var done = assert.async();
                assert.expect(0);
                var badParams = {
                    src: 'some-url',
                    token: 'some-token'
                };
                var request1 = client.get(documentParams[0]);
                var request2 = client.get(phast.ResourceLoader.RequestParams.fromObject(badParams));
                var request3 = client.get(documentParams[1]);
                request1.onsuccess = function () {
                    throw 'an error';
                };
                request2.onerror = function () {
                    throw 'another error';
                };
                request3.onsuccess = function () {
                    done();
                };
            });

            QUnit.test('Test ignoring exceptions while dispatching network error', function (assert) {
                var done = assert.async();
                assert.expect(0);
                var client = new phast.ResourceLoader.BundlerServiceClient('bad-url');
                var request1 = client.get(documentParams[0]);
                var request2 = client.get(documentParams[1]);
                request1.onerror = function () {
                    throw 'an error';
                };
                request2.onerror = function () {
                    done();
                };
            });
        });

        QUnit.module('IndexedDBResourceCache', function (hooks) {

            var Cache = phast.ResourceLoader.IndexedDBResourceCache;
            var Request = phast.ResourceLoader.Request;

            Cache.cleanupProbability = 0;

            function cleanupDB(cb) {
                cb();
            }

            hooks.beforeEach(function () {
                Cache.closeDB();
                Cache.dbName = 'test' + Date.now();
            });

            QUnit.test('Check fetching files with client', function (assert) {
                assert.timeout(2000);
                var done = assert.async(documentParams.length);
                cleanupDB(function () {
                    var cache = new Cache(client);
                    checkFetchingFiles(assert, cache, done);
                });
            });

            QUnit.test('Check retrieval from cache', function (assert) {
                assert.timeout(2000);
                var done = assert.async(documentParams.length);
                cleanupDB(function () {
                    var cache = new Cache(client);
                    var filesFetched = 0;
                    documentParams.forEach(function (params) {
                        var request = cache.get(params);
                        request.onsuccess = function () {
                            filesFetched++;
                        };
                    });
                    wait(
                        assert,
                        function () {
                            return filesFetched === documentParams.length;
                        },
                        function () {
                            var cache = new Cache({
                                get: function () {
                                    throw 'Calling client when should have fetched from cache';
                                }
                            });
                            checkFetchingFiles(assert, cache, done);
                        }
                    );
                })
            });

            QUnit.test('Check handling missing store when retrieving from', function (assert) {
                assert.timeout(2000);
                var done = assert.async();
                cleanupDB(function () {
                    var dbOpenRequest = Cache.openDB(false);
                    dbOpenRequest.onsuccess = function (db) {
                        db.close();
                        var request = new Cache(client).get(documentParams[0]);
                        request.onsuccess = function (responseText) {
                            assert.equal('text-1-contents', responseText);
                            done();
                        };
                    };
                });
            });

            QUnit.test('Check cache cleanup', function (assert) {
                assert.timeout(2000);
                assert.expect(0);
                var done = assert.async(3);
                cleanupDB(function () {
                    var slowClient = {
                        multiplier: 1,
                        get: function (params) {
                            var request = new Request();
                            setTimeout(function () {
                                request.success('Token: ' + params.token);
                            }, this.multiplier++ * 100);
                            return request;
                        }
                    };
                    var caching = new Cache(slowClient);

                    var fetchedCnt = 0;
                    function fetched() {
                        fetchedCnt++;
                    }

                    caching.get({token: 1}).onsuccess = fetched;
                    caching.get({token: 2}).onsuccess = fetched;
                    caching.get({token: 3}).onsuccess = fetched;

                    wait(assert, function () { return fetchedCnt === 3; }, function () {
                        Cache.maybeCleanup(90, 1);

                        setTimeout(function () {
                            var dummyClient = {
                                get: function () {
                                    var request = new Request();
                                    request.error();
                                    return request;
                                }
                            };
                            var nonCaching = new Cache(dummyClient);
                            nonCaching.get({token: 1}).onerror = done;
                            nonCaching.get({token: 2}).onerror = done;
                            nonCaching.get({token: 3}).onsuccess = done;
                        }, 200);

                    });
                });
            });

        });

        function checkFetchingFiles(assert, client, done) {
            done = done || assert.async(documentParams.length);
            assert.expect(documentParams.length);
            documentParams.forEach(function (params, idx) {
                var request = client.get(params);
                request.onsuccess = function (responseText) {
                    assert.equal('text-' + (idx + 1) + '-contents', responseText, 'Contents are correct');
                    done();
                };
            });
        }

    });


});
