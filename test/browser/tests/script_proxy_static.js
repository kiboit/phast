test("script_proxy_static.php", function (assert, document) {
  var h1s = document.getElementsByTagName("h1");
  assert.equal(h1s.length, 1, "There should be one <H1> element on the page");
  assert.equal(
    h1s[0].textContent,
    "Hello, World!",
    "The <H1> should say 'Hello, World!'"
  );

  var scripts = document.getElementsByTagName("script");
  assert.ok(
    scripts.length >= 1,
    "There should be one or more <SCRIPT> elements on the page"
  );
  assert.ok(
    /script_proxy\.js/.test(scripts[0].src),
    "The first <SCRIPT> src should contain the name of the script"
  );
  assert.ok(
    !/phast\.php/.test(scripts[0].src),
    "The first <SCRIPT> src should not contain phast.php"
  );
});
