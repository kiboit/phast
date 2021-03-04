test("script_proxy_php.php", function (assert, document) {
  var h1s = document.getElementsByTagName("h1");
  assert.equal(h1s.length, 1, "There should be one <H1> element on the page");
});
