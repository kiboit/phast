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
    var scripts = getScriptsInExecutionOrder();
    if (scripts.length === 0) {
        return;
    }
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
    scripts.forEach(function (el) {
        var script = document.createElement('script');
        Array.prototype.forEach.call(el.attributes, function (attr) {
            if (/^data-phast-|^defer$/i.test(attr.nodeName)) {
                return;
            }
            script.setAttribute(attr.nodeName, attr.nodeValue);
        });
        script.removeAttribute('type');
        if (el.hasAttribute('data-phast-original-type')) {
            script.setAttribute('type', el.getAttribute('data-phast-original-type'));
        }
        if (!el.hasAttribute('src')) {
            script.setAttribute('src', 'data:,;');
            script.addEventListener('load', function () {
                script.textContent = el.textContent;
                // See: http://perfectionkills.com/global-eval-what-are-the-options/
                (1,eval)(el.textContent);
            });
        }
        if (!el.hasAttribute('async') || !el.hasAttribute('src')) {
            script.async = false;
        }
        if (!el.hasAttribute('async') && !el.hasAttribute('defer')) {
            fakeDocumentWrite(el, script);
        }
        replaceElement(el, script);
        script.removeAttribute('src');
        if (el.hasAttribute('data-phast-original-src')) {
            script.setAttribute('src', el.getAttribute('data-phast-original-src'));
        }
        lastScript = script;
    });
    lastScript.addEventListener('load',  restoreReadyState);
    lastScript.addEventListener('error', restoreReadyState);
}
function getScriptsInExecutionOrder() {
    var immediate = [];
    var deferred = [];
    phast.forEachSelectedElement('script[type="phast-script"]', function (script) {
        if (script.hasAttribute('src') && script.hasAttribute('defer')) {
            deferred.push(script);
        } else {
            immediate.push(script);
        }
    });
    return immediate.concat(deferred);
}
function replaceElement(original, rewritten) {
    insertBefore.call(original.parentNode, rewritten, original);
    original.parentNode.removeChild(original);
}
function restoreReadyState() {
    delete document['readyState'];

    triggerEvent(document, 'readystatechange');
    triggerEvent(document, 'DOMContentLoaded');

    if (triggerLoad) {
        triggerEvent(window, 'load');
    }
}
function triggerEvent(on, name) {
    var e = document.createEvent('Event');
    e.initEvent(name, true, true);
    on.dispatchEvent(e);
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
    var cleanup = phast.once(function () {
        delete document['write'];
    });
    newScript.addEventListener('load',  cleanup);
    newScript.addEventListener('error', cleanup);
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
