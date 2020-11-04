const sectionNav = document.getElementsByClassName("section-nav-elem"),
  addToBag = document.getElementsByClassName("add-to-bag"),
  decrement = document.getElementsByClassName("minus-icon"),
  increment = document.getElementsByClassName("plus-icon");
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

Array.from(addToBag).forEach((btn) => {
  btn.onclick = () =>
    updateBag(
      btn.previousSibling.previousSibling.getElementsByClassName("amount")[0],
      btn.getAttribute("data-id")
    );
});

Array.from(decrement).forEach((btn) => {
  btn.onclick = () => {
    if (btn.nextSibling.nextSibling.innerText > 1)
      decrementAmount(btn.nextSibling.nextSibling);
  };
});

Array.from(increment).forEach((btn) => {
  btn.onclick = () => incrementAmount(btn.previousSibling.previousSibling);
});

function updateBag(amount, id) {
  let xhr = new XMLHttpRequest(),
    fd = new FormData();
  fd.append("bag", JSON.stringify({ amount: amount.innerText, id: id }));

  xhr.open("POST", "/update-cart");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        amount.innerText = 1;
        console.log("Added to bag");
        if (xhr.responseText === "Added")
          document.getElementsByClassName("bag-counter")[0].innerHTML =
            parseInt(
              document.getElementsByClassName("bag-counter")[0].innerHTML
            ) + 1;
      } else {
        alert("There was problem adding to bag. Reload page and try again.");
      }
    }
  };
  xhr.send(fd);
}

function decrementAmount(elem) {
  elem.innerText = parseInt(elem.innerText) - 1;
}

function incrementAmount(elem) {
  elem.innerText = parseInt(elem.innerText) + 1;
}
