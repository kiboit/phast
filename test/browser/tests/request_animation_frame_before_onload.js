test("request_animation_frame_before_onload.php", function (assert, document) {
  assert.deepEqual(document.defaultView.events, [
    "requestAnimationFrame",
    "DOMContentLoaded",
    "requestAnimationFrame via DOMContentLoaded",
    "load",
  ]);
});
