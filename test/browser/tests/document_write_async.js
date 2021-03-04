test("document_write_async.php", function (assert, document) {
  assert.equal(
    document.defaultView.WRITES,
    2,
    "document.write should've been called twice"
  );
  assert.equal(
    document.querySelectorAll("h1").length,
    0,
    "There should be no H1 elements"
  );
});
