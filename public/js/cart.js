function addToCart(product) {
  const cart = array();
  cart.push(product);
  size = cart.length;
  return size;
}

function giveCartContent() {}

function giveSize() {}

const obj = document.getElementById("product");
obj.addEventListener("click", function(e) {
  if (e.target.className === "productContainer") {
    const product = document.getElementById("prod_id");
    addToCart(product);
  }
});
