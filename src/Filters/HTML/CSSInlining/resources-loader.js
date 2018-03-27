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
            } catch (e) {};
        });
    }

    function PackItem(request, params) {
        this.request = request;
        this.params = params;
    }

};

(function () {

    var Request = phast.ResourceLoader.Request;

    var logPrefix = "[Phast] Resource loader:";
    var storeName = 'resources';
    var dbVersion = 1;
    var dbConnection = null;
    var dbPromise = null;
    var itemTTL = 7 * 86400000;

    phast.ResourceLoader.IndexedDBResourceCache = function (client) {

        this.get = function (params) {
            return getFromCache(params).catch(function () {
                return getFromClient(params);
            });
        };

        function getFromClient(params) {
            return client.get(params).then(function (responseText) {
                storeInCache(params, responseText);
                return responseText;
            });
        }

        maybeCleanup(itemTTL, Cache.cleanupProbability);

    };

    var Cache = phast.ResourceLoader.IndexedDBResourceCache;

    Cache.getDB = getDB;
    Cache.openDB = openDB;
    Cache.closeDB = closeDB;
    Cache.dropDB = dropDB;
    Cache.maybeCleanup = maybeCleanup;

    Cache.cleanupProbability = 0.05;
    Cache.dbName = 'phastResourcesCache';

    function getFromCache(params) {
        return new Promise(function (resolve, reject) {
            setTimeout(reject, 250);
            getDB().then(function (db) {
                try {
                    var storeRequest = db
                        .transaction(storeName)
                        .objectStore(storeName)
                        .get(params.token);
                    storeRequest.onerror = function (e) {
                        console.error(logPrefix, 'Error while trying to read from cache:', e);
                        reject();
                    };
                    storeRequest.onsuccess = function () {
                        if (storeRequest.result) {
                            resolve(storeRequest.result.content);
                            storeInCache(storeRequest.result, storeRequest.result.content);
                        } else {
                            reject();
                        }
                    };
                } catch (e) {
                    console.error(logPrefix, 'Exception while trying to read from cache:', e);
                    setTimeout(reject);
                    dropDB();
                }
            }).catch(function (e) {
                console.error(logPrefix, 'Error while opening database:', e);
                reject();
            });
        });
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
        return new Promise(function (resolve, reject) {

            if (dbConnection) {
                resolve(dbConnection);
            }

            var dbOpenRequest = indexedDB.open(Cache.dbName, dbVersion);
            if (createSchema) {
                dbOpenRequest.onupgradeneeded = function () {
                    createDB(dbOpenRequest.result);
                };
            }
            dbOpenRequest.onsuccess = function () {
                dbConnection = dbOpenRequest.result;
                resolve(dbConnection);
            };
            dbOpenRequest.onerror = function () {
                console.error(logPrefix, "Error while opening database:", dbOpenRequest.error);
                reject();
            };
        });
    }

    function createDB(db) {
        var store = db.createObjectStore(storeName, {keyPath: 'token'});
        store.createIndex('lastUsed', 'lastUsed');
    }

    function dropDB() {
        Cache.closeDB();
        return indexedDB.deleteDatabase(Cache.dbName);
    }

    function closeDB() {
        if (dbConnection) {
            dbConnection.close();
            dbConnection = null;
            dbPromise = null;
        }
    }

    function cleanUp(itemTTL) {
        console.debug(logPrefix, "Cleaning up...");
        var maxTime = Date.now() - itemTTL;
        getDB().then(function (db) {
            var store = db.transaction(storeName, 'readwrite').objectStore(storeName);
            var cursorRequest = store.index('lastUsed')
                .openCursor(IDBKeyRange.upperBound(maxTime, true));
            cursorRequest.onsuccess = function (ev) {
                var cursor = ev.target.result;
                if (cursor) {
                    store.delete(cursor.value.token);
                    cursor.continue();
                }
            };
        });
    }

    function maybeCleanup(itemTTL, cleanupProbability) {
        if (Math.random() < cleanupProbability) {
            try {
                cleanUp(itemTTL);
            } catch (e) {
                console.error(logPrefix, "Error while cleaning up:", e);
            }
        }
    }

})();

phast.ResourceLoader.make = function (serviceUrl) {
    var client = new phast.ResourceLoader.BundlerServiceClient(serviceUrl);
    return new phast.ResourceLoader.IndexedDBResourceCache(client);
};




