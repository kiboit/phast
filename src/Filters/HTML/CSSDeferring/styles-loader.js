var scripts = document.querySelectorAll('script[type="phast-link"]');

Array.prototype.forEach.call(scripts, function(script) {
    var replacement = document.createElement('div');
    replacement.innerHTML = script.textContent;
    while (replacement.firstChild) {
        script.parentNode.insertBefore(replacement.firstChild, script);
    }
    script.parentNode.removeChild(script);
});
