<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Jajan Yuk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-image: url('asset/img/bg.jpg');
      background-size: cover;
      background-position: center;
      backdrop-filter: blur(5px);
    }
    .food-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .cart-item {
      transition: all .3s ease;
    }
    .cart-item:hover {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body class="bg-white bg-opacity-80 min-h-screen">
  <!-- Header -->
  <header class="text-center py-10 bg-orange-500 text-white w-full">
    <h1 class="text-4xl font-bold mb-2">Jajan Yuk</h1>
    <p class="text-lg">Makanan enak, harga terjangkau!</p>
  </header>

  <!-- Konten -->
<div class="flex justify-center mt-8">
  <div class="bg-white px-6 py-4 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 text-center">Kami Menyediakan</h2>
  </div>
</div>

  <div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Menu -->
      <div class="lg:w-2/3">
      

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Produk -->
          <?php
          $foods = [
            ['id'=>1,'name'=>'Lumpia Lumer Beef','price'=>15000,'img'=>'asset/img/lumpia (1).jpg','alt'=>'Beef'],
            ['id'=>2,'name'=>'Lumpia Lumer Ayam','price'=>12000,'img'=>'asset/img/lumpia (1).jpg','alt'=>'Ayam'],
            ['id'=>3,'name'=>'Jus Buah Segar','price'=>10000,'img'=>'asset/img/jus.jpg','alt'=>'Jus Buah'],
            ['id'=>4,'name'=>'Es Teh Manis','price'=>3000,'img'=>'asset/img/es.jpg','alt'=>'Es Teh'],
          ];
          foreach ($foods as $f):
          ?>
          <div class="food-card bg-white p-6 rounded-lg shadow-md transition-all duration-300"
               data-id="<?= $f['id'] ?>"
               data-name="<?= $f['name'] ?>"
               data-price="<?= $f['price'] ?>">
            <img src="<?= $f['img'] ?>" alt="<?= $f['alt'] ?>" class="w-full h-48 object-cover rounded-md mb-4"/>
            <h3 class="text-xl font-semibold text-gray-800"><?= $f['name'] ?></h3>
            <p class="text-orange-500 font-bold mb-4">Rp <?= number_format($f['price'],0,',','.') ?></p>
            <div class="flex items-center">
              <button class="decrement bg-gray-200 px-3 py-1 rounded-l" onclick="adjustQuantity(this,-1)">-</button>
              <input type="number" class="quantity w-16 text-center border-t border-b border-gray-300 py-1" value="0" min="0"/>
              <button class="increment bg-gray-200 px-3 py-1 rounded-r" onclick="adjustQuantity(this,1)">+</button>
              <button class="ml-auto bg-orange-500 hover:bg-orange-600 text-white px-4 py-1 rounded transition" onclick="addToCart(this)">Tambah</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Keranjang -->
      <div class="lg:w-1/3">
        <div class="bg-white p-6 rounded-lg shadow-md sticky top-4">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Keranjang Belanja</h2>
          <div id="cart-items" class="mb-6">
            <p id="empty-cart-message" class="text-gray-500 text-center py-4">Keranjang belanja masih kosong</p>
          </div>
          <div id="cart-summary" class="border-t pt-4 hidden">
            <div class="flex justify-between mb-2">
              <span class="font-semibold">Subtotal:</span>
              <span id="subtotal">Rp 0</span>
            </div>
            <div class="flex justify-between mb-4">
              <span class="font-semibold">Total:</span>
              <span id="total" class="text-lg font-bold text-orange-600">Rp 0</span>
            </div>
            <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition"
                    onclick="checkout()">
              Bayar via WhatsApp
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    let cart = [];
    function adjustQuantity(btn, change) {
      const input = btn.parentElement.querySelector('.quantity');
      const v = Math.max(0, parseInt(input.value) + change);
      input.value = v;
    }
    function addToCart(btn) {
      const c = btn.closest('.food-card');
      const id = c.dataset.id, name = c.dataset.name, price = +c.dataset.price;
      const q = +c.querySelector('.quantity').value;
      if (q<1) return;
      const idx = cart.findIndex(i=>i.id===id);
      if (idx>=0) cart[idx].quantity += q;
      else cart.push({id,name,price,quantity:q});
      c.querySelector('.quantity').value = 0;
      updateCart();
    }
    function removeFromCart(id) {
      cart = cart.filter(i=>i.id!==id);
      updateCart();
    }
    function updateCartItem(id, qty) {
      qty = +qty;
      const i = cart.find(x=>x.id===id);
      if (!i) return;
      if (qty<=0) removeFromCart(id);
      else { i.quantity = qty; updateCart(); }
    }
    function updateCart() {
      const ci = document.getElementById('cart-items');
      const cs = document.getElementById('cart-summary');
      if (!cart.length) {
        ci.innerHTML = '<p id="empty-cart-message" class="text-gray-500 text-center py-4">Keranjang belanja masih kosong</p>';
        cs.classList.add('hidden'); return;
      }
      cs.classList.remove('hidden');
      let html = '', sub=0;
      cart.forEach(item=>{
        const tot=item.price*item.quantity; sub+=tot;
        html+=`
          <div class="cart-item flex justify-between items-center py-3 border-b">
            <div><h4 class="font-medium">${item.name}</h4><p class="text-sm text-gray-600">Rp ${item.price.toLocaleString()}</p></div>
            <div class="flex items-center">
              <button class="decrement bg-gray-200 px-2 py-1 rounded-l" onclick="updateCartItem('${item.id}',${item.quantity-1})">-</button>
              <input type="number" class="w-12 text-center border-t border-b border-gray-300 py-1" value="${item.quantity}" min="1" onchange="updateCartItem('${item.id}',this.value)"/>
              <button class="increment bg-gray-200 px-2 py-1 rounded-r" onclick="updateCartItem('${item.id}',${item.quantity+1})">+</button>
              <button class="text-red-500 ml-4" onclick="removeFromCart('${item.id}')">&times;</button>
            </div>
          </div>`;
      });
      ci.innerHTML = html;
      document.getElementById('subtotal').textContent = `Rp ${sub.toLocaleString()}`;
      document.getElementById('total').textContent = `Rp ${sub.toLocaleString()}`;
    }
    function checkout() {
  if (!cart.length) return;
  let msg = "Halo, saya ingin memesan:\n\n", tot = 0;
  cart.forEach(i => {
    tot += i.price * i.quantity;
    msg += `- ${i.name} (${i.quantity}x) = Rp ${ (i.price * i.quantity).toLocaleString() }\n`;
  });
  msg += `\nTotal: Rp ${tot.toLocaleString()}\n\nSaya ingin memesan ini, bagaimana cara pembayarannya?`;
  window.open(`https://wa.me/6285282408835?text=${encodeURIComponent(msg)}`, '_blank');
}

  </script>
</body>
</html>