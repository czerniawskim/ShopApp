function pick(id) {
  const star = document.getElementById("star-" + id);
  const stars = document.getElementsByClassName("selected");
  while (stars.length > 0) {
    stars[0].classList.remove("selected");
  }

  for (let i = 1; i <= id; i++) {
    document.getElementById("star-" + i).classList.add("selected");
  }
}
