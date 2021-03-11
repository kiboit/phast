<?php
usleep((int) $_GET['i'] * .1 * 1e6);
header('Content-Type: text/javascript');
?>
var type = <?= json_encode($_GET['t']); ?>;

console.log("From", type, "script:", document.currentScript.dataset.value);

if (document.currentScript.dataset.value === type) {
  if (!window.OK) {
    window.OK = {};
  }
  if (!window.OK[type]) {
    window.OK[type] = 0;
  }
  window.OK[type]++;
}
