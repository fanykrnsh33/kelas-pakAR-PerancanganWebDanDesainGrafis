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
  let msg = "Halo, saya ingin memesan:\n\n", tot=0;
  cart.forEach(i=>{
    tot+=i.price*i.quantity;
    msg += `- ${i.name} (${i.quantity}x) = Rp ${ (i.price*i.quantity).toLocaleString() }\n`;
  });
  msg += `\nTotal: Rp ${tot.toLocaleString()}\n\nSaya ingin memesan ini, bagaimana cara pembayarannya?`;
  window.open(`https://wa.me/6285282408835?text=${encodeURIComponent(msg)}`, '_blank');
}
