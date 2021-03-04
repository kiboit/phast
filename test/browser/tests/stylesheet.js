test("stylesheet.php", function (assert, document) {
  var h1 = document.getElementsByTagName("h1")[0];
  var cs = document.defaultView.getComputedStyle(h1, null);
  var done = assert.async();
  wait();
  function wait() {
    if (document.querySelectorAll("style[data-phast-params]").length > 0) {
      return setTimeout(wait);
    }
    done();
    assert.equal(
      cs.backgroundColor,
      "rgb(0, 0, 255)",
      "The <h1> should have a blue background"
    );
  }
});
