var Promise = phast.ES6Promise.Promise;

phast.ResourceLoader = {};

phast.ResourceLoader.RequestParams = function (faulty) {

    this.isFaulty = function () {
        return faulty;
    };
};

phast.ResourceLoader.RequestParams.fromString = function(string) {
    try {
        var parsed = JSON.parse(string);
        return phast.ResourceLoader.RequestParams.fromObject(parsed);
    } catch (e) {
        return new phast.ResourceLoader.RequestParams(true);
    }
};

phast.ResourceLoader.RequestParams.fromObject = function (parsed) {
    var params = new phast.ResourceLoader.RequestParams(false);
    for (var x in parsed) {
        params[x] = parsed[x];
    }
    return params;
};

phast.ResourceLoader.BundlerServiceClient = function (serviceUrl) {

    var timeoutHandler;
    var accumulatingPack = [];

    this.get = function (params) {
        return new Promise(function (resolve, reject) {
            if (params.isFaulty()) {
                reject();
            } else {
                accumulatingPack.push(new PackItem({success: resolve, error: reject}, params));
                clearTimeout(timeoutHandler);
                timeoutHandler = setTimeout(flush);
            }
        });
    };

    function flush () {
        var pack = accumulatingPack;
        accumulatingPack = [];
        clearTimeout(timeoutHandler);
        makeRequest(pack);
    }

    function makeRequest(pack) {
        var query = packToQuery(pack);
        var errorHandler = function () {
            handleError(pack);
        };
        var successHandler = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                handleResponse(xhr.responseText, pack);
            } else {
                handleError(pack);
            }
        };
        var xhr = new XMLHttpRequest();
        xhr.open('GET', query);
        xhr.addEventListener('error', errorHandler);
        xhr.addEventListener('abort', errorHandler);
        xhr.addEventListener('load', successHandler);
        xhr.send();
    }

    function packToQuery(pack) {
        var glue = serviceUrl.indexOf('?') > -1 ? '&' : '?';
        var parts = [];
        pack.forEach(function (item, idx) {
            for (var key in item.params) {
                if (key === 'isFaulty') {
                    continue;
                }
                parts.push(encodeURIComponent(key) + '_' + idx + '=' + encodeURIComponent(item.params[key]));
            }
        });
        return serviceUrl + glue + parts.join('&');
    }

    function handleError(pack) {
        pack.forEach(function (item) {
            try {
                item.request.error();
            } catch (e) {}
        });
    }

    function handleResponse(responseText, pack) {
        try {
            var responses = JSON.parse(responseText);
        } catch (e) {
            handleError(pack);
            return;
        }
        responses.forEach(function (response, idx) {
            try {
                if (response.status === 200) {
                    pack[idx].request.success(response.content);
                } else {
                    pack[idx].request.error();
                }
            } catch (e) {}
        });
    }

    function PackItem(request, params) {
        this.request = request;
        this.params = params;
    }

};

(function () {

    var logPrefix = "[Phast] Resource loader:";
    var dbName = 'phastResourcesCache';
    var storeName = 'resources';
    var dbVersion = 1;
    var dbPromise = null;
    var itemTTL = 7 * 86400000;

    phast.ResourceLoader.IndexedDBResourceCache = function (client) {

        this.get = function (params) {
            return getFromCache(params).then(
                function (responseText) {
                    if (responseText) {
                        return responseText;
                    }
                    return getFromClient(params);
                },
                function () {
                    return getFromClient(params);
                }
            );
        };

        function getFromClient(params) {
            return client.get(params).then(function (responseText) {
                storeInCache(params, responseText);
                return responseText;
            });
        }

        setTimeout(function () {
            maybeCleanup(itemTTL, Cache.cleanupProbability);
        }, 5000);

    };

    var Cache = phast.ResourceLoader.IndexedDBResourceCache;

    Cache.getDB = getDB;
    Cache.openDB = openDB;
    Cache.dropDB = dropDB;
    Cache.maybeCleanup = maybeCleanup;
    Cache.setDBName = setDBName;

    Cache.cleanupProbability = 0.05;

    function getFromCache(params) {
        return getDB().then(readFromStore, connectionError);

        function readFromStore(db) {
            try {
                var storeRequest = db
                    .transaction(storeName)
                    .objectStore(storeName)
                    .get(params.token);
            } catch (e) {
                console.error(logPrefix, 'Exception while trying to read from cache:', e);
                dropDB();
                throw e;
            }
            return requestToPromise(storeRequest)
                .then(function (result) {
                    if (result) {
                        setTimeout(function () {
                            storeInCache(result, result.content);
                        }, 1000);
                        return result.content;
                    }
                })
                .catch(function (e) {
                    console.error(logPrefix, 'Error while trying to read from cache:', e);
                    throw e;
                });
        }

        function connectionError(e) {
            console.error(logPrefix, 'Error while opening database:', e);
            throw e;
        }
    }

    function storeInCache(params, responseText) {
        getDB().then(function (db) {
            db.transaction(storeName, 'readwrite')
                .objectStore(storeName)
                .put({
                    token: params.token,
                    content: responseText,
                    lastUsed: Date.now()
                });
        }).catch(function (e) {
            console.error(logPrefix, 'Exception while trying to write to cache:', e);
        });
    }

    function getDB() {
        if (!dbPromise) {
            dbPromise = openDB(true);
        }
        return dbPromise;
    }

    function openDB(createSchema) {
        var dbOpenRequest = indexedDB.open(dbName, dbVersion);
        if (createSchema) {
            dbOpenRequest.onupgradeneeded = function () {
                createDB(dbOpenRequest.result);
            };
        }
        return requestToPromise(dbOpenRequest).then(
            function (db) {
                db.onversionchange = function () {
                    console.debug(logPrefix, 'Closing DB');
                    db.close();
                    if (dbPromise) {
                        dbPromise = null;
                    }
                };
                return db;
            },
            function () {
                console.error(logPrefix, "Error while opening database:", dbOpenRequest.error);
                throw dbOpenRequest.error;
            }
        );
    }

    function createDB(db) {
        var store = db.createObjectStore(storeName, {keyPath: 'token'});
        store.createIndex('lastUsed', 'lastUsed');
    }

    function dropDB() {
        return indexedDB.deleteDatabase(dbName);
    }

    function cleanUp(itemTTL) {
        console.debug(logPrefix, "Cleaning up...");
        var maxTime = Date.now() - itemTTL;
        return getDB()
            .then(function (db) {
                var store = db
                    .transaction(storeName)
                    .objectStore(storeName);
                var cursorRequest = store
                    .index('lastUsed')
                    .openCursor(IDBKeyRange.upperBound(maxTime, true));
                return new Promise(function (resolve, reject) {
                    var deletes = [];
                    cursorRequest.onsuccess = function (ev) {
                        var cursor = ev.target.result;
                        if (cursor) {
                            deletes.push(requestToPromise(
                                db
                                    .transaction(storeName, 'readwrite')
                                    .objectStore(storeName)
                                    .delete(cursor.value.token)
                            ));
                            cursor.continue();
                        } else {
                            resolve(deletes);
                        }
                    };
                    cursorRequest.onerror = reject;
                });
            })
            .then(function (deletes) {
                return Promise.all(deletes);
            })
            .catch(function (e) {
                console.error(logPrefix, "Got error while cleaning up", e);
            });
    }

    function maybeCleanup(itemTTL, cleanupProbability) {
        if (Math.random() < cleanupProbability) {
            return cleanUp(itemTTL);
        }
        return Promise.resolve();
    }

    function requestToPromise(request) {
        return new Promise(function (resolve, reject) {
            request.onerror = function () {
                reject(request.error);
            };
            request.onsuccess = function () {
                resolve(request.result);
            };
        });
    }

    function setDBName(name) {
        dbName = name;
        dbPromise = null;
    }

})();

phast.ResourceLoader.make = function (serviceUrl) {
    var client = new phast.ResourceLoader.BundlerServiceClient(serviceUrl);
    return new phast.ResourceLoader.IndexedDBResourceCache(client);
};




