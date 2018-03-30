var Promise = phast.ES6Promise.Promise;

phast.ResourceLoader = function (client, cache) {

    this.get = get;

    function get(params) {
        return cache.get(params.token)
            .then(function (content) {
                if (content) {
                    cache.set(params.token, content);
                    return content;
                }
                return client.get(params)
                    .then(function (responseText) {
                        cache.set(params.token, responseText);
                        return responseText;
                    });
            })
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
    var accumulatingPack = {};

    this.get = function (params) {
        return new Promise(function (resolve, reject) {
            if (params.isFaulty()) {
                reject();
            } else {
                addToPack(new PackItem({success: resolve, error: reject}, params));
                clearTimeout(timeoutHandler);
                timeoutHandler = setTimeout(flush);
            }
        });
    };

    function addToPack(packItem) {
        if (!accumulatingPack[packItem.params.token]) {
            accumulatingPack[packItem.params.token] = {
                params: packItem.params,
                requests: [packItem.request]
            };
        } else {
            accumulatingPack[packItem.params.token]
                .requests
                .push(packItem.request);
        }
    }

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
        getSortedTokens(pack).forEach(function (token, idx) {
            for (var key in pack[token].params) {
                if (key === 'isFaulty') {
                    continue;
                }
                parts.push(encodeURIComponent(key) + '_' + idx + '=' + encodeURIComponent(pack[token].params[key]));
            }
        });
        return serviceUrl + glue + parts.join('&');
    }

    function handleError(pack) {
        Object.values(pack).forEach(function (item) {
            item.requests.forEach(function (request) {
                request.error();
            });
        });
    }

    function handleResponse(responseText, pack) {
        try {
            var responses = JSON.parse(responseText);
        } catch (e) {
            handleError(pack);
            return;
        }

        var tokens = getSortedTokens(pack);
        responses.forEach(function (response, idx) {
            if (response.status === 200) {
                pack[tokens[idx]].requests.forEach(function (request) {
                    request.success(response.content)
                });
            } else {
                pack[tokens[idx]].requests.forEach(function (request) {
                    request.error();
                });
            }
        });
    }

    function getSortedTokens(pack) {
        return Object.keys(pack).sort();
    }

    function PackItem(request, params) {
        this.request = request;
        this.params = params;
    }

};

phast.ResourceLoader.IndexedDBStorage = function (params) {

    var Storage = phast.ResourceLoader.IndexedDBStorage;
    var logPrefix = Storage.logPrefix;
    var r2p = Storage.requestToPromise;

    var con;

    connect();

    this.get = function (key) {
        return con.get()
            .then(function (db) {
                return r2p(getStore(db).get(key));
            })
            .catch(function (e) {
                console.error(logPrefix, 'Error reading from store:', e);
                resetConnection();
                throw e;
            });
    };

    this.store = function (item) {
        return con.get()
            .then(function (db) {
                return r2p(getStore(db, 'readwrite').put(item));
            })
            .catch(function (e) {
                console.error(logPrefix, 'Error writing to store:', e);
                resetConnection();
                throw e;
            });
    };

    this.delete = function (item) {
        return con.get()
            .then(function (db) {
                return r2p(getStore(db, 'readwrite').delete(item.token));
            })
            .catch(function (e) {
                console.error(logPrefix, 'Error deleting from store:', e);
                resetConnection();
                throw e;
            });
    };

    this.iterateOnAll = function (callback) {
        return con.get()
            .then(function (db) {
                return iterateOnRequest(callback, getStore(db).openCursor());
            })
            .catch(function (e) {
                console.error(logPrefix, 'Error iterating on all:', e);
                resetConnection();
                throw e;
            });
    };

    this.iterateOnLastUsedBefore = function (callback, time) {
        return con.get()
            .then(function (db) {
                var request = getStore(db)
                    .index('lastUsed')
                    .openCursor(IDBKeyRange.upperBound(time, true));
                return iterateOnRequest(callback, request);
            })
            .catch(function (e) {
                console.error(logPrefix, 'Error iterating on used before: ', e);
                resetConnection();
                throw e;
            });
    };

    function iterateOnRequest(callback, request) {
        return new Promise(function (resolve, reject) {
            request.onsuccess = function (ev) {
                var cursor = ev.target.result;
                if (cursor) {
                    callback(cursor.value);
                    cursor.continue();
                } else {
                    resolve();
                }
            };
            request.onerror = reject;
        });
    }

    function getStore(db, mode) {
        mode = mode || 'readonly';
        return db
            .transaction(params.storeName, mode)
            .objectStore(params.storeName);
    }

    function resetConnection() {
        var dropPromise = con.dropDB().then(connect);
        con = {
            get: function () {
                return Promise.reject(new Error('Resetting DB'))
            },

            dropDB: function () {
                return dropPromise;
            }
        };

    }

    function connect() {
        con = new phast.ResourceLoader.IndexedDBStorage.Connection(params);
    }

};

phast.ResourceLoader.IndexedDBStorage.logPrefix = '[Phast] Resource loader:';

phast.ResourceLoader.IndexedDBStorage.requestToPromise = function (request) {
    return new Promise(function (resolve, reject) {
        request.onsuccess = function () {
            resolve(request.result);
        };
        request.onerror = function () {
            reject(request.error);
        };
    });
};

phast.ResourceLoader.IndexedDBStorage.ConnectionParams = function () {
    this.dbName = 'phastResourcesCache';
    this.dbVersion = 1;
    this.storeName = 'resources';
};

phast.ResourceLoader.IndexedDBStorage.StoredResource = function (token, content) {
    this.token = token;
    this.content = content;
    this.lastUsed = Date.now();
};

phast.ResourceLoader.IndexedDBStorage.Connection = function (params) {

    var logPrefix = phast.ResourceLoader.IndexedDBStorage.logPrefix;
    var r2p = phast.ResourceLoader.IndexedDBStorage.requestToPromise;

    var dbPromise;

    this.get = get;

    this.dropDB = dropDB;

    function get() {
        if (!dbPromise) {
            dbPromise = openDB(params);
        }
        return dbPromise;
    }

    function dropDB() {
        return get().then(function (db) {
            console.log(logPrefix, 'Dropping DB');
            db.close();
            dbPromise = null;
            return r2p(indexedDB.deleteDatabase(params.dbName));
        });
    }

    function openDB(params) {
        var request = indexedDB.open(params.dbName, params.dbVersion);
        request.onupgradeneeded = function (db) {
            createSchema(request.result, params);
        };

        return r2p(request)
            .then(function (db) {
                db.onversionchange = function () {
                    console.debug(logPrefix, 'Closing DB');
                    db.close();
                    if (dbPromise) {
                        dbPromise = null;
                    }
                };
                return db;
            })
            .catch(function (e) {
                console.error(logPrefix, "Error while opening database:", e);
                throw e;
            }
        );
    }

    function createSchema(db, params) {
        var store = db.createObjectStore(params.storeName, {keyPath: 'token'});
        store.createIndex('lastUsed', 'lastUsed');
    }

};

phast.ResourceLoader.StorageCache = function (params, storage) {

    var StoredResource = phast.ResourceLoader.IndexedDBStorage.StoredResource;

    this.get = get;
    this.set = set;
    this.maybeCleanup = maybeCleanup;

    var storageSize = null;

    if (params.autoCleanup) {
        setTimeout(maybeCleanup, params.cleanupDelay);
    }

    function get(token) {
        return storage.get(token)
            .then(function (item) {
                if (item) {
                    item.lastUsed = Date.now();
                    storage.store(item);
                    return Promise.resolve(item.content);
                }
                return Promise.resolve();
            });
    }

    function set(token, content) {
        return getCurrentStorageSize()
            .then(function (size) {
                var newSize = content.length + size;
                if (newSize > params.maxStorageSize) {
                    return Promise.reject(new Error('Storage quota will be exceeded'));
                }
                storageSize = newSize;
                var item = new StoredResource(token, content);
                return storage.store(item);
            })
    }

    function maybeCleanup() {
        if (Math.random() < params.cleanupProbability) {
            return cleanUp();
        }
        return Promise.resolve();
    }

    function cleanUp() {
        var maxAge = Date.now() - params.itemTTL;
        var deletePromises = [];
        return storage
            .iterateOnLastUsedBefore(function (item) {
                deletePromises.push(storage.delete(item));
            }, maxAge)
            .then(function () {
                return Promise.all(deletePromises);
            });
    }

    function getCurrentStorageSize() {
        if (storageSize !== null) {
            return Promise.resolve(storageSize);
        }
        var size = 0;
        return storage
            .iterateOnAll(function (item) {
                size += item.content.length;
            })
            .then(function () {
                storageSize = size;
                return Promise.resolve(storageSize);
            });
    }

};

phast.ResourceLoader.StorageCache.StorageCacheParams = function () {
    this.itemTTL = 7 * 86400000;
    this.cleanupProbability = 0.05;
    this.cleanupDelay = 5000;
    this.autoCleanup = true;
    this.maxStorageSize = 4.5 * 1024 * 1024;
};


phast.ResourceLoader.make = function (serviceUrl) {
    var storageParams = new phast.ResourceLoader.IndexedDBStorage.ConnectionParams();
    var storage = new phast.ResourceLoader.IndexedDBStorage(storageParams);
    var cacheParams = new phast.ResourceLoader.StorageCache.StorageCacheParams();
    var cache = new phast.ResourceLoader.StorageCache(cacheParams, storage);
    var client = new phast.ResourceLoader.BundlerServiceClient(serviceUrl);
    return new phast.ResourceLoader(client, cache);
};




