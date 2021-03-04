test("stylesheet_external.php", function (assert, document) {
  var h1 = document.getElementsByTagName("h1")[0];
  var cs = document.defaultView.getComputedStyle(h1, null);
  wait(
    assert,
    function () {
      return document.querySelectorAll('link[media="all"]').length > 0;
    },
    function () {
      assert.equal(
        cs.backgroundColor,
        "rgb(0, 0, 255)",
        "The <h1> should have a blue background"
      );
    }
  );
});
