phast.ResourceLoader = {};

phast.ResourceLoader.Request = function () {

    var onsuccess,
        onerror,
        onend,
        successText,
        success,
        resolved = false,
        called = false;

    Object.defineProperty(this, 'onsuccess', {

        get: function () {
            return onsuccess;
        },

        set: function (func) {
            onsuccess = func;
            if (resolved && success) {
                onsuccess(successText);
            }
        }
    });

    Object.defineProperty(this, 'onerror', {

        get: function () {
            return onerror;
        },

        set: function (func) {
            onerror = func;
            if (resolved && !success) {
                onerror();
            }
        }
    });

    Object.defineProperty(this, 'onend', {

        get: function () {
            return onend;
        },

        set: function (func) {
            onend = func;
            if (resolved) {
                onend();
            }
        }
    });

    this.success = function (responseText) {
        resolved = true;
        successText = responseText;
        success = true;
        if (!called && onsuccess) {
            onsuccess(responseText);
        }
        end();
        called = true;
    };

    this.error = function () {
        resolved = true;
        success = false;
        if (!called && onerror) {
            onerror();
        }
        end();
        called = true;
    };

    function end() {
        if (!called && onend) {
            onend();
        }
    }
};

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
        var request = new phast.ResourceLoader.Request();
        if (params.isFaulty()) {
            request.error();
            return request;
        }
        accumulatingPack.push(new PackItem(request, params));
        clearTimeout(timeoutHandler);
        timeoutHandler = setTimeout(this.flush);
        return request;
    };

    this.flush = function () {
        var pack = accumulatingPack;
        accumulatingPack = [];
        clearTimeout(timeoutHandler);
        makeRequest(pack);
    };

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
    var dbConnectionRequests = [];
    var itemTTL = 7 * 86400000;

    phast.ResourceLoader.IndexedDBResourceCache = function (client) {

        this.get = function (params) {
            var request = new Request();
            var cacheRequest = getFromCache(params);
            cacheRequest.onsuccess = request.success;
            cacheRequest.onerror = function () {
                getFromClient(params, request);
            };
            return request;
        };

        function getFromClient(params, request) {
            var clientRequest = client.get(params);
            clientRequest.onerror = request.error;
            clientRequest.onsuccess = function (responseText) {
                storeInCache(params, responseText);
                request.success(responseText);
            };
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
        var request = new Request();
        var dbRequest = getDB();
        dbRequest.onerror = function (e) {
            console.error(logPrefix, 'Error while opening database:', e);
            request.error();
        };
        dbRequest.onsuccess = function (db) {
            try {
                var storeRequest = db
                    .transaction(storeName)
                    .objectStore(storeName)
                    .get(params.token);
                storeRequest.onerror = function (e) {
                    console.error(logPrefix, 'Error while trying to read from cache:', e);
                    request.error();
                };
                storeRequest.onsuccess = function () {
                    if (storeRequest.result) {
                        request.success(storeRequest.result.content);
                        storeInCache(storeRequest.result, storeRequest.result.content);
                    } else {
                        request.error();
                    }
                };
            } catch (e) {
                console.error(logPrefix, 'Exception while trying to read from cache:', e);
                var drop = dropDB();
                drop.onsuccess = callError;
                drop.onerror = callError;
                function callError() {
                    request.error();
                }
            }
        };
        return request;
    }

    function storeInCache(params, responseText) {
        var dbRequest = getDB();
        try {
            dbRequest.onsuccess = function (db) {
                db.transaction(storeName, 'readwrite')
                    .objectStore(storeName)
                    .put({
                        token: params.token,
                        content: responseText,
                        lastUsed: Date.now()
                    });
            };
        } catch (e) {
            console.error(logPrefix, 'Exception while trying to write to cache:', e);
        }
    }

    function getDB() {
        var request = new Request();
        if (dbConnection) {
            request.success(dbConnection);
        } else {
            dbConnectionRequests.push(request);
            if (dbConnectionRequests.length === 1) {
                openDB(true);
            }
        }
        return request;
    }

    function openDB(createSchema) {
        var request = new Request();
        dbConnectionRequests.push(request);

        var dbOpenRequest = indexedDB.open(Cache.dbName, dbVersion);
        if (createSchema) {
            dbOpenRequest.onupgradeneeded = function () {
                createDB(dbOpenRequest.result);
            };
        }
        dbOpenRequest.onsuccess = function () {
            dbConnection = dbOpenRequest.result;
            callStoredConnectionRequests(function (request) {
                request.success(dbOpenRequest.result);
            });
        };
        dbOpenRequest.onerror = function () {
            console.error(logPrefix, "Error while opening database:", dbOpenRequest.error);
            callStoredConnectionRequests(function (request) {
                request.error();
            });
        };
        return request;
    }

    function callStoredConnectionRequests(cb) {
        var requests = dbConnectionRequests;
        while (requests.length) {
            cb(requests.shift());
        }
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
        }
    }

    function cleanUp(itemTTL) {
        console.debug(logPrefix, "Cleaning up...");
        var maxTime = Date.now() - itemTTL;
        getDB().onsuccess = function (db) {
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
        };
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




