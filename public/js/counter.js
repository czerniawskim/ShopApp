const inp = document.getElementById("form_Quantity");

inp.addEventListener("focus", function() {
  const price = document.getElementById("prod-price").getAttribute("data-val");
  inp.addEventListener("keyup", function() {
    let quant = inp.value;
    let sum = price * quant;
    document.getElementById("counter").innerText =
      quant + " x " + price + " = " + sum;
  });
});
