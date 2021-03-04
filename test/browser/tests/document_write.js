test("document_write.php", function (assert, document) {
  expectedHeaders = ["H1", "H2", "H3"];

  assert.equal(
    getTagNamesInDocumentOrder(expectedHeaders).join(","),
    expectedHeaders.join(","),
    "Headers should be on the page in the right count and order"
  );

  expectedHeaders.forEach(function (name) {
    var elements = document.getElementsByTagName(name);
    assert.equal(
      elements.length,
      1,
      "There should be one <" + name.toUpperCase() + "> element on the page"
    );
    assert.equal(
      elements[0].textContent,
      "Hello, World!",
      "The <" + name.toUpperCase() + "> should say 'Hello, World!'"
    );
  });

  function getTagNamesInDocumentOrder(tagNames) {
    var result = [];
    walk(document.body, function (el) {
      if (el.tagName && tagNames.indexOf(el.tagName) !== -1) {
        result.push(el.tagName);
      }
    });
    return result;
  }

  function walk(el, fn) {
    fn(el);
    for (var i = 0; i < el.childNodes.length; i++) {
      walk(el.childNodes[i], fn);
    }
  }
});
