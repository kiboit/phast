window.addEventListener('load', function() {
    window.setTimeout(function() {
        Array.prototype.forEach.call(
            window.document.querySelectorAll('iframe[data-phast-src]'),
            function(el) {
                el.setAttribute('src', el.getAttribute('data-phast-src'));
                el.removeAttribute('data-phast-src');
            }
        );
    }, 30);
});
