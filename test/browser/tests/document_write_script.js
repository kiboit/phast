test("document_write_script.php", function (assert, document) {
  h1s = document.getElementsByTagName("h1");
  assert.equal(h1s.length, 1, "There should be one <h1> on the page");
  assert.equal(
    h1s[0].textContent,
    "Hello, World!",
    'The <h1> should contain the string "Hello, World!"'
  );
});
