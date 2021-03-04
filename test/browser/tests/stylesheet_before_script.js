test("stylesheet_before_script.php", function (assert, document) {
  wait(
    assert,
    function () {
      return typeof document.defaultView.test !== "undefined";
    },
    function () {
      document.defaultView.test(assert);
    }
  );
});
