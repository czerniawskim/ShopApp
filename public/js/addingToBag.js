const addToBag = document.getElementsByClassName("add-to-bag"),
  decrement = document.getElementsByClassName("minus-icon"),
  increment = document.getElementsByClassName("plus-icon");

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
