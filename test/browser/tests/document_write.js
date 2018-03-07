test('document_write.php', function (assert, document) {
    ['h1', 'h2'].forEach(function (name) {
        var elements = document.getElementsByTagName(name);
        assert.equal(elements.length, 1, "There should be one <" + name.toUpperCase() + "> element on the page");
        assert.equal(elements[0].textContent, 'Hello, World!', "The <" + name.toUpperCase() + "> should say 'Hello, World!'");
    });
});
