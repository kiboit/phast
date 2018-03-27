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

    Object.defineProperty(this, 'resolved', {
        get: function () {
            return resolved;
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
    var dbConnectionRequests = [];
    var itemTTL = 7 * 86400000;

    phast.ResourceLoader.IndexedDBResourceCache = function (client) {

        this.get = function (params) {
            return new Promise(function (resolve, reject) {
                var cacheRequest = getFromCache(params);
                cacheRequest.then(resolve);
                cacheRequest.catch(function () {
                    getFromClient(params).then(resolve, reject)
                });
            });
        };

        function getFromClient(params) {
            var clientRequest = client.get(params);
            return clientRequest.then(function (responseText) {
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
            setTimeout(function () {
                console.error(logPrefix, "getFromCache timed out (that should never happen)");
                reject();
            }, 250);
            var dbRequest = getDB();
            dbRequest.catch(function (e) {
                console.error(logPrefix, 'Error while opening database:', e);
                reject();
            });
            dbRequest.then(function (db) {
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
                            console.log('Cache miss');
                            reject();
                        }
                    };
                } catch (e) {
                    console.error(logPrefix, 'Exception while trying to read from cache:', e);
                    setTimeout(reject);
                    dropDB();
                }
            });
        });
    }

    function storeInCache(params, responseText) {
        getDB().then(function (db) {
            try {
                db.transaction(storeName, 'readwrite')
                    .objectStore(storeName)
                    .put({
                        token: params.token,
                        content: responseText,
                        lastUsed: Date.now()
                    });
            } catch (e) {
                console.error(logPrefix, 'Exception while trying to write to cache:', e);
            }
        });
    }

    var dbPromise;
    function getDB() {
        if (!dbPromise) {
            dbPromise = openDB(true);
        }
        return dbPromise;
    }

    function openDB(createSchema) {
        return new Promise(function (resolve, reject) {
            //dbConnectionRequests.push(request);

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
                // callStoredConnectionRequests(function (request) {
                //     request.success(dbOpenRequest.result);
                // });
            };
            dbOpenRequest.onerror = function () {
                console.error(logPrefix, "Error while opening database:", dbOpenRequest.error);
                reject();
                // callStoredConnectionRequests(function (request) {
                //     request.error();
                // });
            };
        });
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




