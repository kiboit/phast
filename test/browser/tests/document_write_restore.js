test("document_write_restore.php", function (assert, document) {
  var initialElements = document.getElementsByTagName("h1");
  assert.equal(
    initialElements.length,
    1,
    "There should be one h1 on the page before document.write"
  );

  document.defaultView.callDocumentWrite();

  var elements = document.getElementsByTagName("h1");
  assert.equal(
    elements.length,
    0,
    "There should be no h1 on the page after document.write"
  );
});
