test("script_proxy_dynamic.php", function (assert, document) {
  wait(
    assert,
    function () {
      return document.getElementsByTagName("h1").length > 0;
    },
    function () {
      var h1s = Array.prototype.slice.call(document.getElementsByTagName("h1"));
      assert.equal(
        h1s.length,
        3,
        "There should be three <H1> elements on the page"
      );

      h1s.sort(function (a, b) {
        return a.textContent > b.textContent
          ? 1
          : a.textContent < b.textContent
          ? -1
          : 0;
      });

      for (var i = 0; i < 2; i++) {
        assert.equal(
          h1s[i].textContent,
          "Hello, World!",
          "The <H1> should say 'Hello, World!'"
        );
      }

      assert.equal(
        h1s[i].textContent,
        "Hi from module!",
        "The third <H1> should say 'Hi from module!'"
      );

      var scripts = Array.prototype.slice.call(
        document.getElementsByTagName("script")
      );

      scripts.sort(function (a, b) {
        return parseInt(a.dataset.testIndex) - parseInt(b.dataset.testIndex);
      });

      assert.ok(
        scripts[0].hasAttribute("data-phast-rewritten"),
        "The first <SCRIPT> was rewritten"
      );
      assert.ok(
        scripts[1].hasAttribute("data-phast-rewritten"),
        "The second <SCRIPT> was rewritten"
      );
      assert.ok(
        !scripts[2].hasAttribute("data-phast-rewritten"),
        "The third <SCRIPT> was not rewritten"
      );
    }
  );
});
