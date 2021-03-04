test("multiple_set_timeout_before_onload.php", function (assert, document) {
  events = document.defaultView.events;
  events = events.filter(function (v, i) {
    return i === events.lastIndexOf(v);
  });
  assert.deepEqual(events, [
    "setTimeout",
    "DOMContentLoaded",
    "setTimeout via DOMContentLoaded",
    "load",
  ]);
});
