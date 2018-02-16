phast.stylesLoading = 0;

phast.forEachSelectedElement('style[data-phast-href]', function (style) {
    phast.stylesLoading++;
    retrieve(style.getAttribute('data-phast-href'), function (css) {
        style.textContent = css;
        style.removeAttribute('data-phast-href');
    }, function () {
        // TODO: Use a wrapper to make sure this really just happens once
        phast.stylesLoading--;
        console.log("Phast: One down! Waiting for ", phast.stylesLoading, " stylesheets");
        if (phast.stylesLoading === 0 && phast.onStylesLoaded) {
            phast.onStylesLoaded();
        }
    });
});

if (phast.stylesLoading) {
    console.log("Phast: Waiting for ", phast.stylesLoading, " stylesheets");
}

function retrieve(url, fn, always) {
    var req = new XMLHttpRequest();
    req.addEventListener('load', load);
    req.addEventListener('error', error);
    req.addEventListener('abort', error);
    req.open('GET', url);
    req.send();
    function load() {
        if (req.status >= 200 && req.status < 300) {
            fn(req.responseText);
        }
        always();
    }
    function error() {
        always();
    }
}
