const removeProd = document.getElementsByClassName("bag-item-remove");

Array.from(removeProd).forEach((btn) => {
  btn.onclick = () => deleteProd(btn.parentElement.getAttribute("data-id"));
});

function deleteProd(id) {
  let xhr = new XMLHttpRequest(),
    fd = new FormData();
  fd.append("item", id);

  xhr.open("POST", "/remove-bag-element");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        console.info(xhr.responseText);
        if (xhr.responseText === "Removed") removeHTMLElement();
      } else
        alert(
          "There was problem removing from bag. Reload page and try again."
        );
    }
  };
  xhr.send(fd);
}

function removeHTMLElement() {}
