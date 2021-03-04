test("document_write_read.php", function (assert, document) {
  assert.ok(document.defaultView.TEST_RESULT, "TEST_RESULT is defined");
  assert.ok(
    document.defaultView.TEST_RESULT.FOUND_H1,
    "<h1> should be visible right after write"
  );
  var h1s = document.getElementsByTagName("h1");
  assert.equal(h1s.length, 1, "There should be 1 <h1> element on the page");
  assert.equal(
    h1s[0].textContent,
    "Hello, World!",
    'The <h1> should say "Hello, World!"'
  );
});
