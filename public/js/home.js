const sectionNav = document.getElementsByClassName("section-nav-elem");
let last = 1;

Array.from(sectionNav).forEach((btn) => {
  btn.onclick = () =>
    changeDisplay(
      btn.getAttribute("data-current"),
      btn.parentElement.parentElement,
      btn.parentElement
    );
});

function changeDisplay(id, container, nav) {
  Array.from(container.getElementsByClassName("home-section-elem")).forEach(
    (el) => {
      el.style.display = el.classList.contains(`list-${id}`) ? "flex" : "none";
    }
  );

  Array.from(nav.children).forEach((b) => {
    if (b.classList.contains(`no-${last}`)) {
      b.style.fill = "rgb(91, 91, 91)";
      b.style.cursor = "pointer";
    } else if (b.classList.contains(`no-${id}`)) {
      b.style.fill = "#8f2d56";
      b.style.cursor = "default";
    }
  });

  last = id;
}
