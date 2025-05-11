window.onload = function () {
    fetch("getproducts.php")
      .then(res => res.json())
      .then(products => {
        let html = "";
        products.forEach(p => {
          html += `
            <div class="product">
              <img src="${p.image_url}" alt="${p.name}" />
              <h3>${p.name}</h3>
              <p>${p.description}</p>
              <strong>₹${p.price}</strong>
              <button onclick="addToCart(${p.product_id})">Add to Cart</button>
            </div>`;
        });
        document.getElementById("product-list").innerHTML = html;
      });
  };
  
  function addToCart(id) {
    fetch("addtocart.php", {
      method: "POST",
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `product_id=${id}&quantity=1`
    }).then(res => res.text()).then(msg => alert(msg));
  }
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').then(function(registration) {
      console.log('Service Worker registered with scope:', registration.scope);
    }).catch(function(error) {
      console.log('Service Worker registration failed:', error);
    });
  }
  
  