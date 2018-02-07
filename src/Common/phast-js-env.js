phast.forEachSelectedElement = function (selector, callback) {
    Array.prototype.forEach.call(
        window.document.querySelectorAll(selector),
        callback
    );
};
