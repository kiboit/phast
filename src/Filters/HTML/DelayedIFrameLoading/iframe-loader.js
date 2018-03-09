window.addEventListener('load', function() {
    window.setTimeout(loadIframes, 30);
});

function loadIframes() {
    phast.forEachSelectedElement('iframe[data-phast-src]', function(el) {
        var originalSrc = el.getAttribute('data-phast-src');
        el.removeAttribute('data-phast-src');
        if (el.getAttribute('src') === 'about:blank') {
            el.setAttribute('src', originalSrc);
        }
    });
}
