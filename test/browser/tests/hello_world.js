test("hello_world.php", function (assert, document) {
  var h1s = document.getElementsByTagName("h1");
  assert.equal(h1s.length, 1, "There is one <H1> element on the page");
  assert.equal(
    h1s[0].textContent,
    "Hello, World!",
    "The <H1> says 'Hello, World!'"
  );
});
