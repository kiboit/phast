loadPhastJS(['public/es6-promise.js', 'public/resources-loader.js'], function (phast) {

    var Promise = phast.ES6Promise.Promise;

    var indexedDBTest = typeof indexedDB === 'undefined' ? QUnit.skip : QUnit.test;

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
            client = new phast.ResourceLoader.BundlerServiceClient('phast.php?service=bundler', {'src': 's'});
        });

        QUnit.module('RequestParams', function () {

            QUnit.test('Check creating from string', function (assert) {
                var string = JSON.stringify({key1: 'value1', key2: 'value2'});
                var params = phast.ResourceLoader.RequestParams.fromString(string);
                assert.equal(params.key1, 'value1', 'key1 is set right');
                assert.equal(params.key2, 'value2', 'key2 is set right');
            });

            QUnit.test('Check bad params', function (assert) {
                var params = phast.ResourceLoader.RequestParams.fromString('invalid json');
                assert.ok(params === phast.ResourceLoader.RequestParams.FaultyParams, 'FaultyParams is returned');
            });
        });

        QUnit.module('BundlerServiceClient', function () {

            QUnit.test('Test fetching files', function (assert) {
                checkFetchingFiles(assert, client);
            });

            QUnit.test('Test fetching from dedicated service file', function (assert) {
                var done = assert.async();
                var client = new phast.ResourceLoader.BundlerServiceClient('bundler.php', {'src': 's'});
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
                    var params = {fail: 'yes'};
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
                var client = new phast.ResourceLoader.BundlerServiceClient('does-not-exist', {'src': 's'});
                for (var i = 0; i < iterations; i++) {
                    var params = {};
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
                var request2 = client.get(badParams);
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
                var client = new phast.ResourceLoader.BundlerServiceClient('bad-url', {'src': 's'});
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

        QUnit.module('IndexedDBStorage', function (hooks) {

            var Storage = phast.ResourceLoader.IndexedDBStorage;

            var params = new Storage.ConnectionParams();
            hooks.beforeEach(function () {
                params.dbName = 'test-' + Date.now();
            });

            QUnit.module('IndexedDBStorage.Connection', function () {

                indexedDBTest('Test connection making', function (assert) {
                    var done = getDoneCB(assert);
                    var connection = new Storage.Connection(params);
                    connection.get()
                        .then(function (db) {
                            assert.ok(db instanceof IDBDatabase, 'Resolves with IDBDatabase');
                            done();
                        })
                        .catch(function (e) {
                            assert.ok(false,'Got error: ' + e);
                            done();
                        });
                });

                indexedDBTest('Test storage creation', function (assert) {
                    var done = getDoneCB(assert);
                    new Storage.Connection(params).get()
                        .then(function (db) {
                            var store = db
                                .transaction(params.storeName)
                                .objectStore(params.storeName);
                            assert.equal('token', store.keyPath, 'Proper keypath');
                            done();
                        })
                        .catch(function (e) {
                            assert.ok(false, 'Got error: ' + e);
                            done();
                        });
                });

                indexedDBTest('Test getting the same connection per instance', function (assert) {
                    var done = getDoneCB(assert);
                    assert.expect(2);
                    var con1 = new Storage.Connection(params);
                    var con2 = new Storage.Connection(params);
                    var db1;
                    con1.get()
                        .then(function (db) {
                            db1 = db;
                            return con1.get();
                        })
                        .then(function (db) {
                            assert.ok(db1 === db, 'Reuses connection');
                            return con2.get();
                        })
                        .then(function (db) {
                            assert.ok(db1 !== db, 'Connection is not singleton');
                        })
                        .finally(done);
                });

                indexedDBTest('Test dropping database', function (assert) {
                    var done = getDoneCB(assert);
                    var con = new Storage.Connection(params);
                    var db1;
                    con.get()
                        .then(function (db) {
                            db1 = db;
                            return con.dropDB();
                        })
                        .then(function () {
                            return con.get();
                        })
                        .then(function (db) {
                            assert.ok(db1 !== db, 'New db instance created');
                        })
                        .catch(function (e) {
                            assert.ok(false, 'Got error: ' + e);
                        }).finally(done)
                });

            });

            indexedDBTest('Test storing and fetching', function (assert) {
                var done = getDoneCB(assert);
                var testItem = new Storage.StoredResource('the-token', 'the-content');
                var storage = new Storage(params);
                storage.get(testItem.token)
                    .then(function (item) {
                        assert.notOk(item, 'Did not find anything');
                        return storage.store(testItem);
                    })
                    .then(function () {
                        return storage.get(testItem.token);
                    })
                    .then(function (retrieved) {
                        assert.propEqual(testItem, retrieved, 'Correctly stored and retrieved');
                        storage = new Storage(params);
                        return storage.get(testItem.token);
                    })
                    .then(function (retrieved) {
                        assert.propEqual(testItem, retrieved, 'Correctly retrieved from a new instance');
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
                    })
                    .finally(done);
            });

            indexedDBTest('Test clearing the store', function (assert) {
                var done = getDoneCB(assert, 2000, 3);
                var storage = new Storage(params);
                var iterations = [1, 2, 3];
                var lastPromise = Promise
                    .all(iterations.map(function (value) {
                        return storage.store({token: 't' + value, content: 'some-content'});
                    }))
                    .then(function () {
                        return storage.clear();
                    });
                iterations.forEach(function (value) {
                    lastPromise = lastPromise
                        .then(function () {
                            return storage.get('t' + value);
                        })
                        .then(function (content) {
                            assert.notOk(content, 't' + value + ' is cleared');
                        })
                        .catch(function (e) {
                            assert.ok(false, 'Got error: ' + e);
                        })
                        .finally(done);
                });
            });

            indexedDBTest('Test iterating on all items', function (assert) {
                var done = getDoneCB(assert);
                var items = [0, 1, 2].map(function (value) {
                    return new Storage.StoredResource('token-' + value, 'content-' + value);
                });
                var storage = new Storage(params);
                var iteratedOn = [];
                Promise
                    .all(items.map(function (item) {
                        return storage.store(item);
                    }))
                    .then(function () {
                        return storage.iterateOnAll(function (item) {
                            iteratedOn.push(item);
                        });
                    })
                    .then(function () {
                        iteratedOn.sort(function (a, b) {
                            return a.token < b.token ? -1 : 1;
                        });

                        assert.equal(items.length, iteratedOn.length, 'Fetched number matches');
                        iteratedOn.forEach(function (item, idx) {
                            assert.propEqual(items[idx], item, 'Matching reulst ' + idx);
                        });
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
                    })
                    .finally(done);
            });

            indexedDBTest('Test handling missing store', function (assert) {
                var done = getDoneCB(assert);
                indexedDB.open(params.dbName, params.dbVersion).onsuccess = function (ev) {
                    ev.target.result.close();
                    var testItem = new Storage.StoredResource('the-token', 'content');
                    var storage = new Storage(params);
                    storage.store(testItem)
                        .catch(function () {
                            return storage.store(testItem);
                        })
                        .catch(function (e) {
                            assert.equal('Resetting DB', e.message);
                            return wait(200)();
                        })
                        .then(function () {
                            storage.store(testItem);
                        })
                        .then(function () {
                            return storage.get(testItem.token);
                        })
                        .then(function (item) {
                            assert.propEqual(testItem, item, 'Retrieved correctly');
                        })
                        .catch(function (e) {
                            assert.ok(false, 'Got error: ' + e);
                        })
                        .finally(done);
                };

            });

        });

        QUnit.module('StorageCache', function (hooks) {

            var cacheParams,
                storageParams,
                storage;

            var Cache = phast.ResourceLoader.StorageCache;

            hooks.beforeEach(function () {
                storageParams = new phast.ResourceLoader.IndexedDBStorage.ConnectionParams();
                storage = new phast.ResourceLoader.IndexedDBStorage(storageParams);
                storageParams.dbName = 'test-' + Date.now();
                cacheParams = new phast.ResourceLoader.StorageCache.StorageCacheParams();
            });


            indexedDBTest('Test obeying size quota', function (assert) {
                cacheParams.maxStorageSize = 146;

                var done = getDoneCB(assert);
                var cache = new Cache(cacheParams, storage);
                cache.set('t1', makeStringOfSize(10))
                    .then(function () {
                        return cache.set('t2', makeStringOfSize(147));
                    })
                    .then(function () {
                        assert.ok(false, 'Obeying quota');
                    })
                    .catch(function () {
                        assert.ok(true, 'Obeying quota');
                    })
                    .then(function () {
                        return cache.get('t1');
                    })
                    .then(function (content) {
                        assert.ok(content, 'Cleanup not triggered');
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
                    })
                    .finally(done);
            });

            indexedDBTest('Test cleaning up upon quota reach', function (assert) {
                cacheParams.maxStorageSize = 80;

                var content = makeStringOfSize(50);
                var cache = new Cache(cacheParams, storage);
                var done =  getDoneCB(assert);
                cache.set('t1', content)
                    .then(function () {
                        return cache.set('t2', content);
                    })
                    .then(function () {
                        return cache.get('t2');
                    })
                    .then(function (content) {
                        assert.ok(content, 'Content is read');
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
                    })
                    .then(function () {
                        return cache.get('t1');
                    })
                    .then(function (content) {
                        assert.notOk(content, 'First record has been deleted');
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
                    })
                    .finally(done);
            });

            function makeStringOfSize(size) {
                var str = '';
                for (var i = 0; i < size; i++) {
                    str += 'a';
                }
                return str;
            }

        });

        QUnit.module('ResourceLoader', function () {

            var cacheParams,
                storageParams,
                storage;

            var Cache = phast.ResourceLoader.StorageCache;

            var fakeCache = {
                get: function () {
                    return Promise.resolve();
                },

                set: function () {
                    return Promise.resolve();
                }
            };

            var fakeClient = {
                get: function () {
                    return Promise.reject();
                }
            };

            hooks.beforeEach(function () {
                storageParams = new phast.ResourceLoader.IndexedDBStorage.ConnectionParams();
                storage = new phast.ResourceLoader.IndexedDBStorage(storageParams);
                storageParams.dbName = 'test-' + Date.now();
                cacheParams = new phast.ResourceLoader.StorageCache.StorageCacheParams();
                cacheParams.autoCleanup = false;
            });

            QUnit.test('Get from client', function (assert) {
                var done = getDoneCB(assert, 2000, documentParams.length);
                var loader = new phast.ResourceLoader(client, fakeCache);
                checkFetchingFiles(assert, loader, done);
            });

            indexedDBTest('Get from cache', function (assert) {
                var done = getDoneCB(assert, 2000, documentParams.length);
                var storage = new phast.ResourceLoader.IndexedDBStorage(storageParams);
                var cache = new Cache(cacheParams, storage);
                var loader = new phast.ResourceLoader(client, cache);
                Promise
                    .all(documentParams.map(function (params) {
                        return loader.get(params);
                    }))
                    .then(wait(300))
                    .then(function () {
                        loader = new phast.ResourceLoader(fakeClient, cache);
                        checkFetchingFiles(assert, loader, done);
                    })
                    .catch(function (e) {
                        assert.ok(false, 'Got error: ' + e);
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

        function getDoneCB(assert, timeout, callsCount) {
            timeout = timeout || 2000;
            callsCount = callsCount || 1;
            assert.timeout(timeout);
            return assert.async(callsCount);
        }

        function wait(time) {
            return function () {
                return new Promise(function (resolve) {
                    setTimeout(resolve, time);
                });
            }
        }

    });


});
