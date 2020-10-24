const left = document.getElementsByClassName("left-btn-home"),
  right = document.getElementsByClassName("right-btn-home"),
  addToBag = document.getElementsByClassName("add-to-bag"),
  decrement = document.getElementsByClassName("minus-icon"),
  increment = document.getElementsByClassName("plus-icon");
let display = 1;

Array.from(left).forEach((l) => {
  l.onclick = () => {
    let divs = l.parentElement.getElementsByTagName("div");
    if (display > 0) {
      showElements(--display, divs);
      l.setAttribute("disabled", true);
      l.parentElement.children[
        l.parentElement.children.length - 1
      ].removeAttribute("disabled");
    }
  };
});
Array.from(right).forEach((r) => {
  r.onclick = () => {
    let divs = r.parentElement.getElementsByTagName("div");
    showElements(++display, divs);
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


Array.from(addToBag).forEach(btn => {
  btn.onclick = () => updateBag(btn.previousSibling.previousSibling.getElementsByClassName('amount')[0], btn.getAttribute('data-id'))
})

Array.from(decrement).forEach(btn => {
  btn.onclick = () => {if(btn.nextSibling.nextSibling.innerText > 1) decrementAmount(btn.nextSibling.nextSibling)}
})

Array.from(increment).forEach(btn => {
  btn.onclick = () => incrementAmount(btn.previousSibling.previousSibling)
})

function updateBag(amount, id) {
  let xhr = new XMLHttpRequest,
  fd = new FormData;
  fd.append('bag', JSON.stringify({ amount: amount.innerText, id: id }))

  xhr.open("POST", "/update-cart");
  xhr.onreadystatechange = () => {
    if(xhr.readyState === 4){
      if(xhr.status === 200) {
        amount.innerText = 1
        console.log("Added to bag")
      }
      else {
        alert("There was problem adding to bag. Reload page and try again.")
      }
    }
  }
  xhr.send(fd)
}

function decrementAmount(elem) {
  elem.innerText = parseInt(elem.innerText) - 1
}

function incrementAmount(elem) {
  elem.innerText = parseInt(elem.innerText) + 1
}