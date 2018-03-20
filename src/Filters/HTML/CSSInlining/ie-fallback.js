(function () {
    var ua = window.navigator.userAgent;
    if (ua.indexOf('MSIE ') === -1 && ua.indexOf('Trident/') === -1) {
        return;
    }

    Array.prototype.forEach.call(
        document.querySelectorAll('style[data-phast-ie-fallback-url]'),
        function (el) {
            var link = document.createElement('link');
            if (el.hasAttribute('media')) {
                link.setAttribute('media', el.getAttribute('media'));
            }
            link.setAttribute('rel', 'stylesheet');
            link.setAttribute('href', el.getAttribute('data-phast-ie-fallback-url'));
            el.parentNode.insertBefore(link, el);
            el.parentNode.removeChild(el);
        }
    );
    Array.prototype.forEach.call(
        document.querySelectorAll('style[data-phast-nested-inlined]'),
        function (groupEl) {
            groupEl.parentNode.removeChild(groupEl);
        }
    );
})();
