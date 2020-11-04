const img = document.getElementsByClassName("image-show")[0],
  pickers = document.getElementsByClassName("img-picker"),
  decrement = document.getElementsByClassName("minus-icon")[0],
  increment = document.getElementsByClassName("plus-icon")[0],
  addToBag = document.getElementsByClassName("add-to-bag")[0];
let active = 1;

addToBag.onclick = () =>
  updateBag(
    addToBag.previousSibling.previousSibling.getElementsByClassName(
      "amount"
    )[0],
    addToBag.getAttribute("data-id")
  );

decrement.onclick = () => {
  if (decrement.nextSibling.nextSibling.innerText > 1)
    decrementAmount(decrement.nextSibling.nextSibling);
};

increment.onclick = () =>
  incrementAmount(increment.previousSibling.previousSibling);

Array.from(pickers).forEach((picker) => {
  picker.onclick = () => changeImage(picker.getAttribute("data-src"), picker);
});

function changeImage(src, picker) {
  img.setAttribute("src", src);

  Array.from(pickers).forEach((pkr) => {
    if (pkr.getAttribute("id") == active) {
      pkr.style.backgroundColor = "rgb(91, 91, 91)";
      pkr.style.cursor = "pointer";
    }
  });
  picker.style.backgroundColor = "#8f2d56";
  picker.style.cursor = "default";

  active = picker.getAttribute("id");
}

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
