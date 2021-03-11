if (document.currentScript) {
  window.SYNC_VALUE = document.currentScript.dataset.value;
  window.SYNC_SRC = document.currentScript.getAttribute("src");
}
