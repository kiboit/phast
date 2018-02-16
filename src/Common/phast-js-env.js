var noop = function() {}

if (!window.console) console = {log: noop, error: noop};

phast.forEachSelectedElement = function (selector, callback) {
    Array.prototype.forEach.call(
        window.document.querySelectorAll(selector),
        callback
    );
};

while (phast.scripts.length) {
    (phast.scripts.shift())();
}
