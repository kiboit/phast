/* globals phast */

var Promise = phast.ES6Promise.Promise;

phast.ResourceLoader = function (client, cache) {
  this.get = function (params) {
    return cache
      .get(params)
      .then(function (content) {
        if (typeof content !== "string") {
          throw new Error("response should be string");
        }
        return content;
      })
      .catch(function () {
        var promise = client.get(params);
        promise.then(function (responseText) {
          cache.set(params, responseText);
        });
        return promise;
      });
  };
};

phast.ResourceLoader.RequestParams = {};

phast.ResourceLoader.RequestParams.FaultyParams = {};

phast.ResourceLoader.RequestParams.fromString = function (string) {
  try {
    return JSON.parse(string);
  } catch (e) {
    return phast.ResourceLoader.RequestParams.FaultyParams;
  }
};

phast.ResourceLoader.BundlerServiceClient = function (
  serviceUrl,
  shortParamsMappings,
  pathInfo
) {
  var RequestsPack = phast.ResourceLoader.BundlerServiceClient.RequestsPack;
  var PackItem = RequestsPack.PackItem;

  var accumulatingPack;

  this.get = function (params) {
    if (params === phast.ResourceLoader.RequestParams.FaultyParams) {
      return Promise.reject(new Error("Parameters did not parse as JSON"));
    }
    return new Promise(function (resolve, reject) {
      if (accumulatingPack === undefined) {
        accumulatingPack = new RequestsPack(shortParamsMappings);
      }
      accumulatingPack.add(
        new PackItem({ success: resolve, error: reject }, params)
      );
      setTimeout(flush);
      if (accumulatingPack.toQuery().length > 4500) {
        console.log(
          "[Phast] Resource loader: Pack got too big; flushing early..."
        );
        flush();
      }
    });
  };

  function flush() {
    if (accumulatingPack === undefined) {
      return;
    }
    var pack = accumulatingPack;
    accumulatingPack = undefined;
    makeRequest(pack);
  }

  function makeRequest(pack) {
    var url = phast.buildServiceUrl(
      { serviceUrl: serviceUrl, pathInfo: pathInfo },
      "service=bundler&" + pack.toQuery()
    );
    var errorHandler = function () {
      console.error(
        "[Phast] Request to bundler failed with status",
        xhr.status
      );
      console.log("URL:", url);
      pack.handleError();
    };
    var successHandler = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        pack.handleResponse(xhr.responseText);
      } else {
        pack.handleError();
      }
    };
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.addEventListener("error", errorHandler);
    xhr.addEventListener("abort", errorHandler);
    xhr.addEventListener("load", successHandler);
    xhr.send();
  }
};

phast.ResourceLoader.BundlerServiceClient.RequestsPack = function (
  shortParamsMappings
) {
  var items = {};

  this.getLength = function () {
    var i = 0;
    for (var token in items) {
      i++;
    }
    return i;
  };

  this.add = function (packItem) {
    var key;
    if (packItem.params.token) {
      key = "token=" + packItem.params.token;
    } else if (packItem.params.ref) {
      key = "ref=" + packItem.params.ref;
    } else {
      key = "";
    }
    if (!items[key]) {
      items[key] = {
        params: packItem.params,
        requests: [packItem.request],
      };
    } else {
      items[key].requests.push(packItem.request);
    }
  };

  this.toQuery = function () {
    var parts = [],
      cacheMarkers = [],
      lastSrc = "";
    getSortedTokens().forEach(function (token) {
      var queryKey, queryValue;
      for (var key in items[token].params) {
        if (key === "cacheMarker") {
          cacheMarkers.push(items[token].params.cacheMarker);
          continue;
        }
        queryKey = shortParamsMappings[key] ? shortParamsMappings[key] : key;
        if (key === "strip-imports") {
          queryValue = encodeURIComponent(queryKey);
        } else if (key === "src") {
          queryValue =
            encodeURIComponent(queryKey) +
            "=" +
            encodeURIComponent(compressSrc(items[token].params.src, lastSrc));
          lastSrc = items[token].params.src;
        } else {
          queryValue =
            encodeURIComponent(queryKey) +
            "=" +
            encodeURIComponent(items[token].params[key]);
        }
        parts.push(queryValue);
      }
    });
    if (cacheMarkers.length > 0) {
      parts.unshift("c=" + phast.hash(cacheMarkers.join("|"), 23045));
    }
    return obfuscateQuery(parts.join("&"));
  };

  function getSortedTokens() {
    return Object.keys(items).sort(function (a, b) {
      return gt(a, b) ? 1 : gt(b, a) ? -1 : 0;
    });
    function gt(a, b) {
      if (
        typeof items[a].params.src !== "undefined" &&
        typeof items[b].params.src !== "undefined"
      ) {
        return items[a].params.src > items[b].params.src;
      }
      return a > b;
    }
  }

  function compressSrc(src, lastSrc) {
    var prefixLen = 0,
      maxBase36Val = Math.pow(36, 2) - 1;
    while (
      prefixLen < lastSrc.length &&
      src[prefixLen] === lastSrc[prefixLen]
    ) {
      prefixLen++;
    }
    prefixLen = Math.min(prefixLen, maxBase36Val);
    return toBase36(prefixLen) + "" + src.substr(prefixLen);
  }

  function toBase36(dec) {
    var charsTable = [
      "0",
      "1",
      "2",
      "3",
      "4",
      "5",
      "6",
      "7",
      "8",
      "9",
      "a",
      "b",
      "c",
      "d",
      "e",
      "f",
      "g",
      "h",
      "i",
      "j",
      "k",
      "l",
      "m",
      "n",
      "o",
      "p",
      "q",
      "r",
      "s",
      "t",
      "u",
      "v",
      "w",
      "x",
      "y",
      "z",
    ];
    var p1 = dec % 36;
    var p2 = Math.floor((dec - p1) / 36);
    return charsTable[p2] + charsTable[p1];
  }

  function obfuscateQuery(query) {
    if (!/(^|&)s=/.test(query)) {
      return query;
    }
    return query.replace(/(%..)|([A-M])|([N-Z])/gi, function (m, e, am, nz) {
      if (e) {
        return m;
      }
      return String.fromCharCode(m.charCodeAt(0) + (am ? 13 : -13));
    });
  }

  this.handleResponse = function (responseText) {
    try {
      var responses = JSON.parse(responseText);
    } catch (e) {
      this.handleError();
      return;
    }

    var tokens = getSortedTokens();

    if (responses.length !== tokens.length) {
      console.error(
        "[Phast] Requested",
        tokens.length,
        "items from bundler, but got",
        responses.length,
        "response(s)"
      );
      this.handleError();
      return;
    }

    responses.forEach(function (response, idx) {
      if (response.status === 200) {
        items[tokens[idx]].requests.forEach(function (request) {
          request.success(response.content);
        });
      } else {
        items[tokens[idx]].requests.forEach(function (request) {
          request.error(
            new Error("Got from bundler: " + JSON.stringify(response))
          );
        });
      }
    });
  }.bind(this);

  this.handleError = function () {
    for (var k in items) {
      items[k].requests.forEach(function (request) {
        request.error();
      });
    }
  };
};

phast.ResourceLoader.BundlerServiceClient.RequestsPack.PackItem = function (
  request,
  params
) {
  this.request = request;
  this.params = params;
};

phast.ResourceLoader.IndexedDBStorage = function (params) {
  var Storage = phast.ResourceLoader.IndexedDBStorage;
  var logPrefix = Storage.logPrefix;
  var r2p = Storage.requestToPromise;

  var con;

  connect();

  this.get = function (key) {
    return openStore("readonly").then(function (store) {
      return r2p(store.get(key)).catch(makeResetHandler("reading from store"));
    });
  };

  this.store = function (item) {
    return openStore("readwrite").then(function (store) {
      return r2p(store.put(item)).catch(makeResetHandler("writing to store"));
    });
  };

  this.clear = function () {
    return openStore("readwrite").then(function (store) {
      return r2p(store.clear());
    });
  };

  this.iterateOnAll = function (callback) {
    return openStore("readonly").then(function (store) {
      return iterateOnRequest(callback, store.openCursor()).catch(
        makeResetHandler("iterating on all")
      );
    });
  };

  function openStore(mode) {
    return con.get().then(function (db) {
      try {
        return db
          .transaction(params.storeName, mode)
          .objectStore(params.storeName);
      } catch (e) {
        console.error(
          logPrefix,
          "Could not open store; recreating database:",
          e
        );
        resetConnection();
        throw e;
      }
    });
  }

  function makeResetHandler(description) {
    return function (e) {
      console.error(logPrefix, "Error " + description + ":", e);
      resetConnection();
      throw e;
    };
  }

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

  function resetConnection() {
    var dropPromise = con.dropDB().then(connect);
    con = {
      get: function () {
        return Promise.reject(
          new Error("Database is being dropped and recreated")
        );
      },
      dropDB: function () {
        return dropPromise;
      },
    };
  }

  function connect() {
    con = new phast.ResourceLoader.IndexedDBStorage.Connection(params);
  }
};

phast.ResourceLoader.IndexedDBStorage.logPrefix = "[Phast] Resource loader:";

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
  this.dbName = "phastResourcesCache";
  this.dbVersion = 1;
  this.storeName = "resources";
};

phast.ResourceLoader.IndexedDBStorage.StoredResource = function (
  token,
  content
) {
  this.token = token;
  this.content = content;
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
      console.error(logPrefix, "Dropping DB");
      db.close();
      dbPromise = null;
      return r2p(window.indexedDB.deleteDatabase(params.dbName));
    });
  }

  function openDB(params) {
    if (typeof window.indexedDB === "undefined") {
      return Promise.reject(new Error("IndexedDB is not available"));
    }

    var request = window.indexedDB.open(params.dbName, params.dbVersion);

    request.onupgradeneeded = function () {
      createSchema(request.result, params);
    };

    return r2p(request)
      .then(function (db) {
        db.onversionchange = function () {
          console.debug(logPrefix, "Closing DB");
          db.close();
          if (dbPromise) {
            dbPromise = null;
          }
        };
        return db;
      })
      .catch(function (e) {
        console.log(
          logPrefix,
          "IndexedDB cache is not available. This is usually due to using private browsing mode."
        );
        throw e;
      });
  }

  function createSchema(db, params) {
    db.createObjectStore(params.storeName, { keyPath: "token" });
  }
};

phast.ResourceLoader.StorageCache = function (params, storage) {
  var StoredResource = phast.ResourceLoader.IndexedDBStorage.StoredResource;

  this.get = function (params) {
    return get(paramsToToken(params));
  };
  this.set = function (params, content) {
    return set(paramsToToken(params), content, false);
  };

  var storageSize = null;

  function paramsToToken(params) {
    return JSON.stringify(params);
  }

  function get(token) {
    return storage.get(token).then(function (item) {
      if (item) {
        return Promise.resolve(item.content);
      }
      return Promise.resolve();
    });
  }

  function set(token, content, noRetry) {
    return getCurrentStorageSize().then(function (size) {
      var newSize = content.length + size;
      if (newSize > params.maxStorageSize) {
        return noRetry || content.length > params.maxStorageSize
          ? Promise.reject(new Error("Storage quota will be exceeded"))
          : cleanupAndRetrySet(token, content);
      }
      storageSize = newSize;
      var item = new StoredResource(token, content);
      return storage.store(item);
    });
  }

  function cleanupAndRetrySet(token, content) {
    return cleanUp().then(function () {
      return set(token, content, true);
    });
  }

  function cleanUp() {
    return storage.clear().then(function () {
      storageSize = 0;
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
  this.maxStorageSize = 4.5 * 1024 * 1024;
};

phast.ResourceLoader.BlackholeCache = function () {
  this.get = function () {
    return Promise.reject();
  };

  this.set = function () {
    return Promise.reject();
  };
};

phast.ResourceLoader.make = function (
  serviceUrl,
  shortParamsMappings,
  pathInfo
) {
  var cache = makeCache();
  var client = new phast.ResourceLoader.BundlerServiceClient(
    serviceUrl,
    shortParamsMappings,
    pathInfo
  );
  return new phast.ResourceLoader(client, cache);

  function makeCache() {
    var userAgent = window.navigator.userAgent;
    if (/safari/i.test(userAgent) && !/chrome|android/i.test(userAgent)) {
      console.log("[Phast] Not using IndexedDB cache on Safari");
      return new phast.ResourceLoader.BlackholeCache();
    } else {
      var storageParams =
        new phast.ResourceLoader.IndexedDBStorage.ConnectionParams();
      var storage = new phast.ResourceLoader.IndexedDBStorage(storageParams);
      var cacheParams =
        new phast.ResourceLoader.StorageCache.StorageCacheParams();
      return new phast.ResourceLoader.StorageCache(cacheParams, storage);
    }
  }
};
