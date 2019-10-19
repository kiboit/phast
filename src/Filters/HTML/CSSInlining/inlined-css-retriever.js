/* globals phast */

phast.stylesLoading = 0;

var resourceLoader = phast.ResourceLoader.instance;

phast.forEachSelectedElement('style[data-phast-params]', function (style) {
    var textParams = style.getAttribute('data-phast-params');
    var params = phast.ResourceLoader.RequestParams.fromString(textParams);
    phast.stylesLoading++;
    resourceLoader.get(params)
        .then(function (css) {
            style.textContent = css;
            style.removeAttribute('data-phast-params');
        })
        .catch(function (err) {
            console.warn("[Phast] Failed to load CSS", params, err);
            var src = style.getAttribute('data-phast-original-src');
            if (!src) {
                console.error("[Phast] No data-phast-original-src on <style>!", style);
                return;
            }
            console.info("[Phast] Falling back to <link> element for", src);
            var link = document.createElement('link');
            link.href = src;
            link.media = style.media;
            link.rel = 'stylesheet';
            link.addEventListener('load', function () {
                if (style.parentNode) {
                    style.parentNode.removeChild(style);
                }
            });
            style.parentNode.insertBefore(link, style.nextSibling);
        })
        .finally(function () {
            phast.stylesLoading--;
            if (phast.stylesLoading === 0 && phast.onStylesLoaded) {
                phast.onStylesLoaded();
            }
        });
});
