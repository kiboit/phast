var Promise = phast.ES6Promise;

var scripts = document.querySelectorAll('script[type="phast-link"]');

Array.prototype.forEach.call(scripts, function(script) {
    var replacement = document.createElement('div');
    replacement.innerHTML = script.textContent;

    var link = replacement.firstChild;
    if (!link) {
        return;
    }

    var media = link.media;
    link.media = 'only x';

    (new Promise(function (resolve) {
        // This should work on modern browsers.
        try {
            link.addEventListener('load', resolve);
        } catch (e) {}

        // This will work otherwise.
        setTimeout(resolve, 2500);
    }))
        .then(function () {
            link.media = media;
        });

    script.parentNode.insertBefore(link, script);
    script.parentNode.removeChild(script);
});
