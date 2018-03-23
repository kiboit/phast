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

phast.ResourceLoader.IndexedDBResourceCache = function (client) {

    var Request = phast.ResourceLoader.Request;
    var Cache = phast.ResourceLoader.IndexedDBResourceCache;

    this.get = function (params) {
        var request = new Request();
        var cacheRequest = getFromCache(params);
        cacheRequest.onsuccess = request.success;
        cacheRequest.onerror = function () {
            getFromClient(params, request);
        };
        return request;
    };

    function getFromCache(params) {
        var request = new Request();
        var dbRequest = getDB();
        dbRequest.onerror = function () {
            request.error();
        };
        dbRequest.onsuccess = function (db) {
            var storeRequest = db
                .transaction(Cache.storeName)
                .objectStore(Cache.storeName)
                .get(params.token);
            storeRequest.onerror = function () {
                request.error();
            };
            storeRequest.onsuccess = function () {
                if (storeRequest.result) {
                    request.success(storeRequest.result.content);
                } else {
                    request.error();
                }
            };
        };
        return request;
    }

    function getFromClient(params, request) {
        var clientRequest = client.get(params);
        clientRequest.onerror = request.error;
        clientRequest.onsuccess = function (responseText) {
            storeInCache(params, responseText);
            request.success(responseText);
        };
    }

    function storeInCache(params, responseText) {
        var dbRequest = getDB();
        dbRequest.onsuccess = function (db) {
            db.transaction(Cache.storeName, 'readwrite')
                .objectStore(Cache.storeName)
                .put({token: params.token, content: responseText});
        };
    }

    function getDB() {
        var request = new Request();
        if (Cache.dbConnection) {
            request.success(Cache.dbConnection);
        } else {
            Cache.dbConnectionRequests.push(request);
            if (Cache.dbConnectionRequests.length === 1) {
                openDB();
            }
        }
        return request;
    }

    function openDB() {
        var dbOpenRequest = indexedDB.open(
            Cache.dbName,
            Cache.dbVersion
        );
        dbOpenRequest.onupgradeneeded = function () {
            createDB(dbOpenRequest.result);
        };
        dbOpenRequest.onsuccess = function () {
            Cache.dbConnection = dbOpenRequest.result;
            callStoredConnectionRequests(function (request) {
                request.success(dbOpenRequest.result);
            });
        };
        dbOpenRequest.onerror = function () {
            callStoredConnectionRequests(function (request) {
                request.error();
            });
        }
    }

    function callStoredConnectionRequests(cb) {
        var requests = Cache.dbConnectionRequests;
        while (requests.length) {
            cb(requests.shift());
        }
    }

    function createDB(db) {
        db.createObjectStore(Cache.storeName, {keyPath: 'token'});
    }
};

phast.ResourceLoader.IndexedDBResourceCache.close = function () {
    if (phast.ResourceLoader.IndexedDBResourceCache.dbConnection) {
        phast.ResourceLoader.IndexedDBResourceCache.dbConnection.close();
        phast.ResourceLoader.IndexedDBResourceCache.dbConnection = null;
    }
};

phast.ResourceLoader.IndexedDBResourceCache.dbName = 'phastResourcesCache';
phast.ResourceLoader.IndexedDBResourceCache.storeName = 'resources';
phast.ResourceLoader.IndexedDBResourceCache.dbVersion = 1;
phast.ResourceLoader.IndexedDBResourceCache.dbConnection = null;
phast.ResourceLoader.IndexedDBResourceCache.dbConnectionRequests = [];

phast.ResourceLoader.make = function (serviceUrl) {
    var client = new phast.ResourceLoader.BundlerServiceClient(serviceUrl);
    return new phast.ResourceLoader.IndexedDBResourceCache(client);
};




