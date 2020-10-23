const left = document.getElementsByClassName("left-btn-home"),
  right = document.getElementsByClassName("right-btn-home");

Array.from(left).forEach((l) => {
  l.onclick = () => {
    let divs = l.parentElement.getElementsByTagName("div");
    showElements(1, divs);
    l.setAttribute("disabled", true);
    l.parentElement.children[
      l.parentElement.children.length - 1
    ].removeAttribute("disabled");
  };
});
Array.from(right).forEach((r) => {
  r.onclick = () => {
    let divs = r.parentElement.getElementsByTagName("div");
    showElements(2, divs);
    r.setAttribute("disabled", true);
    r.parentElement.children[0].removeAttribute("disabled");
  };
});

function showElements(display, elements) {
  Array.from(elements).forEach((el) => {
    if (el.classList.contains(`list-${display}`)) {
      el.style.display = "flex";
    } else {
      el.style.display = "none";
    }
  });
}
