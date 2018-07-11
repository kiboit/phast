var Promise = phast.ES6Promise;

var scriptIndex = 0;

// Save insertBefore before it is overridden by the scripts proxy filter.
var insertBefore = Element.prototype.insertBefore;

var go = phast.once(loadScripts);

phast.on(document, 'DOMContentLoaded').then(function () {
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
    var lastNewScript;
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

    var promises = [];
    scripts.forEach(function (originalScript, idx) {
        promises.push(getScriptText(originalScript).then(function (scriptText) {
            return Promise.resolve([originalScript, scriptText]);
        }));
    });

    prepareNextPromise();

    function prepareNextPromise() {
        var next = promises.shift();
        next.then(function (data) {
            var originalScript = data[0];
            var scriptText = data[1];
            var lastNewScript = makeReplacementScript(originalScript, scriptText);
            if (promises.length > 0) {
                prepareNextPromise();
            } else {
                lastNewScript.addEventListener('load',  restoreReadyState);
                lastNewScript.addEventListener('error', restoreReadyState);
            }
        });
    }
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

function getScriptText(script) {
    if (script.hasAttribute('src')) {
        return fetchScript(script.getAttribute('src'));
    }
    var body = script.textContent.replace(/^\s*<!--/, '');
    return Promise.resolve(body);
}

function getPromise() {
    return new Promise(function (resolve) {
        window.setTimeout(resolve, 10);
    })
}

function makeReplacementScript(originalScript, scriptText) {
    var newScript = document.createElement('script');
    Array.prototype.forEach.call(originalScript.attributes, function (attr) {
        if (/^data-phast-|^defer$|^src$|^type$/i.test(attr.nodeName)) {
            return;
        }
        newScript.setAttribute(attr.nodeName, attr.nodeValue);
    });
    newScript.textContent = originalScript.textContent;
    newScript.setAttribute('src', 'data:,;');
    newScript.addEventListener('load', function () {
        try {
            // See: http://perfectionkills.com/global-eval-what-are-the-options/
            (1,eval)(scriptText);
        } catch (e) {
            console.error("[Phast] Error in inline script:", e);
            console.log("First 100 bytes of script body:", scriptText.substr(0, 100));
            throw e;
        }
    });
    if (originalScript.hasAttribute('data-phast-original-type')) {
        newScript.setAttribute('type', originalScript.getAttribute('data-phast-original-type'));
    }
    if (!originalScript.hasAttribute('async') || !originalScript.hasAttribute('src')) {
        newScript.async = false;
    }
    if (!originalScript.hasAttribute('async') && !originalScript.hasAttribute('defer')) {
        fakeDocumentWrite(originalScript, newScript);
    }
    replaceElement(originalScript, newScript);
    if (originalScript.hasAttribute('data-phast-original-src')) {
        newScript.setAttribute('src', originalScript.getAttribute('data-phast-original-src'));
    } else if (originalScript.hasAttribute('src')) {
        newScript.setAttribute('src', originalScript.getAttribute('src'));
    }
    return newScript;
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


function fetchScript(url) {
    return new phast.ES6Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                resolve(xhr.responseText);
            } else {
                reject();
            }
        };
        xhr.onerror = reject;
        xhr.send();
    });
}
