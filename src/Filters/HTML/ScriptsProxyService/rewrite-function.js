var config = phast.config['script-proxy-service'];
var urlPattern = /^(https?:)?\/\//;
var cacheMarker = Math.floor((new Date).getTime() / 1000 / config.urlRefreshTime);
var ids = {};
var whitelist = compileWhitelistPatterns(config.whitelist);

phast.scripts.push(function () {
    overrideDOMMethod('appendChild');
    overrideDOMMethod('insertBefore');
});

function compileWhitelistPatterns(patterns) {
    var re = /^(.)(.*)\1([a-z]*)$/i;
    var compiled = [];
    patterns.forEach(function (pattern) {
        var match = re.exec(pattern);
        if (!match) {
            window.console && window.console.log("Phast: Not a pattern:", pattern);
            return;
        }
        try {
            compiled.push(new RegExp(match[2], match[3]));
        } catch (e) {
            window.console && window.console.log("Phast: Failed to compile pattern:", pattern);
        }
    });
    return compiled;
}

function checkWhitelist(value) {
    for (var i = 0; i < whitelist.length; i++) {
        if (whitelist[i].exec(value)) {
            return true;
        }
    }
    return false;
}

function overrideDOMMethod(name) {
    var original = Element.prototype[name];
    Element.prototype[name] = function () {
        processNode(arguments[0]);
        return original.apply(this, arguments);
    };
}

function processNode(el) {
    if (!el) {
        return;
    }
    if (el.nodeType !== Node.ELEMENT_NODE) {
        return;
    }
    if (el.tagName !== 'SCRIPT') {
        return;
    }
    if (!urlPattern.test(el.src)) {
        return;
    }
    if (el.src.substr(0, config.serviceUrl.length) == config.serviceUrl) {
        return;
    }
    if (!checkWhitelist(el.src)) {
        return;
    }
    var id = getScriptId(el.src);
    el.setAttribute('data-phast-proxied-script', id);
    el.src = config.serviceUrl +
        '&src=' + encodeURIComponent(el.src) +
        '&cacheMarker=' + encodeURIComponent(cacheMarker) +
        '&id=' + id;
}

function getScriptId(src) {
    var hash = murmurhash3_32_gc(src);
    ids[hash] = ids[hash] || 0;
    var count = ++ids[hash];
    return 'd-' + hash + '-' + count;
}

/**
 * JS Implementation of MurmurHash3 (r136) (as of May 20, 2011)
 *
 * @author <a href="mailto:gary.court@gmail.com">Gary Court</a>
 * @see http://github.com/garycourt/murmurhash-js
 * @author <a href="mailto:aappleby@gmail.com">Austin Appleby</a>
 * @see http://sites.google.com/site/murmurhash/
 *
 * @param {string} key ASCII only
 * @param {number} seed Positive integer only
 * @return {number} 32-bit positive integer hash
 */

function murmurhash3_32_gc(key, seed) {
    var remainder, bytes, h1, h1b, c1, c1b, c2, c2b, k1, i;

    remainder = key.length & 3; // key.length % 4
    bytes = key.length - remainder;
    h1 = seed;
    c1 = 0xcc9e2d51;
    c2 = 0x1b873593;
    i = 0;

    while (i < bytes) {
        k1 =
            ((key.charCodeAt(i) & 0xff)) |
            ((key.charCodeAt(++i) & 0xff) << 8) |
            ((key.charCodeAt(++i) & 0xff) << 16) |
            ((key.charCodeAt(++i) & 0xff) << 24);
        ++i;

        k1 = ((((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16))) & 0xffffffff;
        k1 = (k1 << 15) | (k1 >>> 17);
        k1 = ((((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16))) & 0xffffffff;

        h1 ^= k1;
        h1 = (h1 << 13) | (h1 >>> 19);
        h1b = ((((h1 & 0xffff) * 5) + ((((h1 >>> 16) * 5) & 0xffff) << 16))) & 0xffffffff;
        h1 = (((h1b & 0xffff) + 0x6b64) + ((((h1b >>> 16) + 0xe654) & 0xffff) << 16));
    }

    k1 = 0;

    switch (remainder) {
        case 3: k1 ^= (key.charCodeAt(i + 2) & 0xff) << 16;
        case 2: k1 ^= (key.charCodeAt(i + 1) & 0xff) << 8;
        case 1: k1 ^= (key.charCodeAt(i) & 0xff);

            k1 = (((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16)) & 0xffffffff;
            k1 = (k1 << 15) | (k1 >>> 17);
            k1 = (((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16)) & 0xffffffff;
            h1 ^= k1;
    }

    h1 ^= key.length;

    h1 ^= h1 >>> 16;
    h1 = (((h1 & 0xffff) * 0x85ebca6b) + ((((h1 >>> 16) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
    h1 ^= h1 >>> 13;
    h1 = ((((h1 & 0xffff) * 0xc2b2ae35) + ((((h1 >>> 16) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
    h1 ^= h1 >>> 16;

    return h1 >>> 0;
}
