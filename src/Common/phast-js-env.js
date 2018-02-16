phast.forEachSelectedElement = function (selector, callback) {
    Array.prototype.forEach.call(
        window.document.querySelectorAll(selector),
        callback
    );
};

phast.once = function (fn) {
    var done = false;
    return function () {
        if (!done) {
            done = true;
            fn.apply(this, Array.prototype.slice(arguments));
        }
    };
};

while (phast.scripts.length) {
    (phast.scripts.shift())();
}
