phast.stylesLoading = 0;

var resourceLoader = new phast.ResourceLoader.make(phast.config.serviceUrl);

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

function retrieveFromBundler(textParams, done, always) {
    var params = phast.ResourceLoader.RequestParams.fromString(textParams);
    resourceLoader.get(params)
        .then(done)
        .finally(always);
}
