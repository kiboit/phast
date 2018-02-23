var scriptIndex = 0;

// Save insertBefore before it is overridden by the scripts proxy filter.
var insertBefore = Element.prototype.insertBefore;

var go = phast.once(loadScripts);

document.addEventListener('DOMContentLoaded', function () {
    if (phast.stylesLoading) {
        phast.onStylesLoaded = go;
        setTimeout(go, 4000);
    } else {
        setTimeout(go);
    }
});

var loadHappened = false;

window.addEventListener('load', function () {
    loadHappened = true;
});

var triggerLoad = false;

function loadScripts() {
    var scripts = document.querySelectorAll('script[type="phast-script"]');
    if (scripts.length === 0) {
        return;
    }
    var deferreds = [];
    var replace = function (original, rewritten) {
        insertBefore.call(original.parentNode, rewritten, original);
        original.parentNode.removeChild(original);
    };
    var lastScript;
    try {
        Object.defineProperty(document, 'readyState', {
            configurable: true,
            get: function() {
                return 'loading';
            }
        });
    } catch (e) {
        window.console && console.error("Phast: Unable to override document.readyState on this browser: ", e);
    }
    if (loadHappened) {
        triggerLoad = true;
    }
    Array.prototype.forEach.call(scripts, function (el) {
        var script = document.createElement('script');
        Array.prototype.forEach.call(el.attributes, function (attr) {
            script.setAttribute(attr.nodeName, attr.nodeValue);
        });
        if (!el.hasAttribute('async')) {
            script.async = false;
        }
        if (el.hasAttribute('data-phast-original-type')) {
            script.setAttribute('type', el.getAttribute('data-phast-original-type'));
            script.removeAttribute('data-phast-original-type');
        } else {
            script.removeAttribute('type');
        }
        if (!el.hasAttribute('src')) {
            script.setAttribute('src', 'data:,;');
            try {
                Object.defineProperty(script, 'src', {
                    configurable: true,
                    get: function() { return ''; }
                });
            } catch (e) {
                window.console && console.error("Phast: Unable to override script.src on this browser: ", e);
            }
            script.addEventListener('load', function () {
                delete script['src'];
                script.removeAttribute('src');
                script.textContent = el.textContent;
                // See: http://perfectionkills.com/global-eval-what-are-the-options/
                (1,eval)(el.textContent);
            });
        }
        if (!el.hasAttribute('async') && !el.hasAttribute('defer')) {
            fakeDocumentWrite(el, script);
        }
        if (el.hasAttribute('src') && el.hasAttribute('defer')) {
            deferreds.push({original: el, rewritten: script});
        } else {
            replace(el, script);
            lastScript = script;
        }
    });
    deferreds.forEach(function (deferred) {
        deferred.rewritten.removeAttribute('defer');
        replace(deferred.original, deferred.rewritten);
        lastScript = deferred.rewritten;
    });
    if (lastScript) {
        lastScript.addEventListener('load',  restoreReadyState);
        lastScript.addEventListener('error', restoreReadyState);
    }
}
function restoreReadyState() {
    delete document['write'];

    delete document['readyState'];

    triggerEvent(document, 'readystatechange');
    triggerEvent(document, 'DOMContentLoaded');

    if (triggerLoad) {
        triggerEvent(window, 'load');
    }

    function triggerEvent(on, name) {
        var e = document.createEvent('Event');
        e.initEvent(name, true, true);
        on.dispatchEvent(e);
    }
}
function exec(func, opt_scopeObject) {
    try {
        func.call(opt_scopeObject || window);
    } catch (err) {}
}
function fakeDocumentWrite(originalScript, newScript) {
    var scriptId = ++scriptIndex;
    newScript.setAttribute('data-phast-script', scriptId);
    var beforeScript = buildScript(
        '(function () {' +
        'delete document["write"];' +
        'var script = document.querySelector("[data-phast-script=\\"' + scriptId + '\\"]");' +
        'if (!script) return;' +
        'script.removeAttribute("data-phast-script");' +
        'var beforeScript = document.querySelector("[data-phast-before-script=\\"' + scriptId + '\\"]");' +
        'if (beforeScript) beforeScript.parentNode.removeChild(beforeScript);' +
        'document.write = function (markup) {' +
        'script.insertAdjacentHTML("afterend", "" + markup);' +
        '};' +
        '})();'
    );
    beforeScript.setAttribute('data-phast-before-script', scriptId);
    originalScript.parentNode.insertBefore(beforeScript, originalScript);
}
function buildScript(body) {
    var script = document.createElement('script');
    script.async = false;
    script.setAttribute('src', 'data:text/javascript;base64,' + utoa(body));
    return script;
}
function utoa(str) {
    return btoa(
        encodeURIComponent(str).replace(
            /%([0-9A-F]{2})/g,
            function toSolidBytes(match, p1) {
                return String.fromCharCode('0x' + p1);
            }
        )
    );
}
