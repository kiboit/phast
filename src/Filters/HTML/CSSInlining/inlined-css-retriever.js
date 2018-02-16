phast.stylesLoading = 0;

phast.forEachSelectedElement('style[data-phast-href]', function (style) {
    phast.stylesLoading++;
    retrieve(style.getAttribute('data-phast-href'), function (css) {
        style.textContent = css;
        style.removeAttribute('data-phast-href');
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
