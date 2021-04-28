test("script_order.php", function (assert, document) {
  var expected = [
    "inline",
    "deferred inline",
    "synchronous external",
    "second inline",
    "proxied defer",
    "deferred external",
  ];
  assert.ok(document.defaultView.order, "window.order is defined");
  var order = document.defaultView.order;
  wait(
    assert,
    function () {
      return order.length >= expected.length + 1;
    },
    function () {
      assert.ok(
        order.indexOf("async external") !== -1,
        "async script was loaded"
      );
      assert.ok(
        order.indexOf("async external") !== 0,
        "async script was not loaded first"
      );
      order.splice(order.indexOf("async external"), 1);
      assert.equal(
        order.length,
        expected.length,
        "" + expected.length + " scripts were loaded"
      );
      var strOrder = order.join(", ");
      var strExpected = expected.join(", ");
      assert.equal(
        strOrder,
        strExpected,
        "Scripts were loaded in the expected order"
      );
    }
  );
});
