const removeProd = document.getElementsByClassName("bag-item-remove"),
  deleteBatch = document.getElementsByClassName("delete-batch")[0],
  checkAll = document.getElementsByClassName("check-all")[0],
  checkboxes = document.getElementsByClassName("item-select"),
  list = document.getElementsByClassName("cart-list")[0],
  adding = document.getElementsByClassName("price-to-add"),
  total = document.getElementsByClassName("total")[0],
  title = document.querySelector("title");
let selected = 0;

Array.from(removeProd).forEach((btn) => {
  btn.onclick = () =>
    deleteProd(btn.parentElement.getAttribute("data-id"), btn.parentElement);
});

checkAll.onclick = () => {
  Array.from(checkboxes).forEach((chk) => {
    if (checkAll.checked) {
      chk.checked = true;
      updateSelectedAmount(checkboxes.length);
      selected = checkboxes.length;
    } else {
      chk.checked = false;
      updateSelectedAmount(0);
      selected = 0;
    }
  });
};

Array.from(checkboxes).forEach((check) => {
  check.onclick = () => {
    if (!check.checked) {
      selected--;
      updateSelectedAmount(selected);
    } else {
      selected++;
      updateSelectedAmount(selected);
    }
  };
});

function deleteProd(id, parent) {
  let xhr = new XMLHttpRequest(),
    fd = new FormData();
  fd.append("item", id);

  xhr.open("POST", "/remove-bag-element");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        console.info(xhr.responseText);
        if (xhr.responseText === "Removed")
          removeHTMLElement(parent.parentElement.children.length - 1, parent);
      } else
        alert(
          "There was problem removing from bag. Reload page and try again."
        );
    }
  };
  xhr.send(fd);
}

function removeHTMLElement(amount, elem) {
  newAmount = --amount;
  elem.parentElement.removeChild(elem);
  Array.from(adding).forEach((add) => {
    if (add.getAttribute("id") === elem.getAttribute("id")) {
      add.parentElement.removeChild(add);
      newTotal =
        parseFloat(total.innerText.split("Total: ").pop().split("$")[0]) -
        parseFloat(add.innerHTML.split("$").pop());
      total.innerHTML = `<b>Total:</b> ${newTotal}$`;
    }
  });
  title.innerText = amount != 1 ? `Cart | ${amount} items` : "Cart | 1 item";
}

function updateSelectedAmount(amount) {
  if (amount === 1) {
    document.getElementsByClassName(
      "selected-amount"
    )[0].innerHTML = `1 item selected`;
  } else {
    document.getElementsByClassName(
      "selected-amount"
    )[0].innerHTML = `${amount} items selected`;
  }
}
