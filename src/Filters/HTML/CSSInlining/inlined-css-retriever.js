phast.stylesLoading = 0;

phast.forEachSelectedElement('style[data-phast-params]', function (style) {
    phast.stylesLoading++;
    retrieveFromBundler(style.getAttribute('data-phast-params'), function (css) {
        style.textContent = css;
        style.removeAttribute('data-phast-params');
    }, phast.once(function () {
        phast.stylesLoading--;
        if (phast.stylesLoading === 0 && phast.onStylesLoaded) {
            phast.onStylesLoaded();
        }
    }));
});

function retrieve(url, done, always) {
    var req = new XMLHttpRequest();
    req.addEventListener('load', load);
    req.addEventListener('error', error);
    req.addEventListener('abort', error);
    req.open('GET', url);
    req.send();
    function load() {
        if (req.status >= 200 && req.status < 300) {
            done(req.responseText);
        }
        always();
    }
    function error() {
        always();
    }
}

function retrieveFromBundler(textParams, done, always) {

    var params = JSON.parse(textParams);
    var parts = [];
    for (var key in params) {
        parts.push(encodeURIComponent(key) + '_0=' + encodeURIComponent(params[key]));
    }
    var url = phast.config.serviceUrl + '&' + parts.join('&');
    retrieve(url, function (responseText) {
        var response = JSON.parse(responseText);
        if (response[0] && response[0].status === 200) {
            done(response[0].content);
        }
    }, always);
}
