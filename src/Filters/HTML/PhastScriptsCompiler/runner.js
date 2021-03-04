phast.config = JSON.parse(atob(phast.config));

while (phast.scripts.length) {
  phast.scripts.shift()();
}
