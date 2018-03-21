phast.ResourceLoader = {};

phast.ResourceLoader.Request = function () {

    var onsuccess,
        onerror,
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
                func(successText);
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
                func();
            }
        }
    });

    this.success = function (responseText) {
        resolved = true;
        successText = responseText;
        success = true;
        if (!called && onsuccess) {
            onsuccess(responseText);
            called = true;
        }
    };

    this.error = function () {
        resolved = true;
        success = false;
        if (!called && onerror) {
            onerror();
            called = true;
        }
    };
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

    this.get = function (params) {
        var request = new phast.ResourceLoader.Request();
        if (params.isFaulty()) {
            request.error();
            return request;
        }
        var url = paramsToQuery(params);
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.addEventListener('error', request.error);
        xhr.addEventListener('abort', request.error);
        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                handleResponse(xhr.responseText, request);
            } else {
                request.error();
            }
        });
        xhr.send();
        return request;
    };

    function paramsToQuery(params) {
        var glue = serviceUrl.indexOf('?') > -1 ? '&' : '?';
        var parts = [];
        for (var key in params) {
            if (key === 'isFaulty') {
                continue;
            }
            parts.push(encodeURIComponent(key) + '_0=' + encodeURIComponent(params[key]));
        }
        return serviceUrl + glue + parts.join('&');
    }

    function handleResponse(responseText, request) {
        try {
            var response = JSON.parse(responseText);
            if (response[0] && response[0].status === 200) {
                request.success(response[0].content);
            } else {
                request.error();
            }
        } catch (e) {
            request.error();
        }
    }


};




