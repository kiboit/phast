test("event_count.php", function (assert, document) {
  assert.propEqual(document.defaultView.events, [
    { event: "readystatechange", readyState: "interactive" },
    { event: "DOMContentLoaded", readyState: "interactive" },
    { event: "readystatechange", readyState: "complete" },
    { event: "load", readyState: "complete" },
  ]);
});
