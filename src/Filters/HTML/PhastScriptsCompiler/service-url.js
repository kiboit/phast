/* globals
     phast
     btoa
*/

phast.buildServiceUrl = function (config, params) {
    if (config.pathInfo) {
        return appendPathInfo(config.serviceUrl, buildQuery(params));
    } else {
        return appendQueryString(config.serviceUrl, buildQuery(params));
    }
};

function buildQuery(params) {
    if (typeof params === 'string') {
        return params;
    }
    var pieces = [];
    for (var k in params) {
        if (params.hasOwnProperty(k)) {
            pieces.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
        }
    }
    return pieces.join('&');
}

function appendPathInfo(url, query) {
    var path = btoa(query)
        .replace(/=/g, '')
        .replace(/\//g, '_')
        .replace(/\+/g, '-');

    return (
        url
            .replace(/\?.*$/, '')
            .replace(/\/__p__\.js$/, '')
        + '/' + path + '.q.js'
    );
}

function appendQueryString(url, queryString) {
    var glue = url.indexOf('?') > -1 ? '&' : '?';
    return url + glue + queryString;
}
