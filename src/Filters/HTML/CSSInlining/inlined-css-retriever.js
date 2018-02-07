phast.forEachSelectedElement('style[data-phast-href]', function (style) {
    retrieve(style.getAttribute('data-phast-href'), function (css) {
        style.textContent = css;
        style.removeAttribute('data-phast-href');
    });
});

function retrieve(url, fn) {
    var req = new XMLHttpRequest();
    req.addEventListener('load', load);
    req.open('GET', url);
    req.send();
    function load() {
        if (req.status >= 200 && req.status < 300) {
            fn(req.responseText);
        }
    }
}
