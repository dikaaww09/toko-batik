'use strict';
document.querySelectorAll('#main-nav a').forEach(link => {
  link.addEventListener('click', () => {
    const menu = document.getElementById('main-nav');
    if (menu.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(menu).hide();
  });
});
document.querySelectorAll('form[data-delete-name]').forEach(form => {
  form.querySelector('[data-delete-button]').disabled = false;
  form.addEventListener('submit', event => {
    if (!window.confirm(`Hapus produk “${form.dataset.deleteName}”? Produk dan gambar unggahannya akan dihapus.`)) event.preventDefault();
  });
});
const upload = document.getElementById('gambar');
const imageUrl = document.getElementById('gambar_url');
const preview = document.getElementById('image-preview');
const originalPreview = preview ? preview.src : undefined;
let previewURL;
const normalizeImageUrl = value => {
  const url = value.trim();
  const drive = url.match(/^https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)(?:\/|$)/);
  return drive ? `https://drive.google.com/uc?export=view&id=${encodeURIComponent(drive[1])}` : url;
};
const refreshImageSource = () => {
  const sourceEl = document.querySelector('input[name="image_source"]:checked');
  if (!sourceEl) return;
  const source = sourceEl.value;
  document.querySelectorAll('[data-image-panel]').forEach(panel => {
    panel.hidden = panel.dataset.imagePanel !== source;
  });
  if (preview) {
    if (source === 'url' && imageUrl && imageUrl.value.trim()) preview.src = normalizeImageUrl(imageUrl.value);
    if (source === 'upload' && (!upload || !upload.files[0])) preview.src = originalPreview;
  }
};
document.querySelectorAll('input[name="image_source"]').forEach(input => input.addEventListener('change', refreshImageSource));
if (imageUrl) imageUrl.addEventListener('input', refreshImageSource);
refreshImageSource();
if (upload) upload.addEventListener('change', () => {
  const file = upload.files[0];
  const status = document.getElementById('image-status');
  status.textContent = '';
  if (previewURL) URL.revokeObjectURL(previewURL);
  previewURL = undefined;
  preview.src = originalPreview;
  if (!file) return;
  if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
    status.textContent = 'Pilih JPG, PNG, atau WebP maksimal 2 MB.';
    upload.value = '';
    return;
  }
  previewURL = URL.createObjectURL(file);
  preview.src = previewURL;
  status.textContent = 'Pratinjau diperbarui. Klik Simpan Produk untuk menyimpan.';
});
const checkoutForm = document.querySelector('form[action$="/checkout"]');
if (checkoutForm) checkoutForm.addEventListener('submit', () => {
  const button = checkoutForm.querySelector('button[type="submit"]');
  if (!button) return;
  button.disabled = true;
  button.textContent = 'Menyimpan Pesanan...';
});
const cartForm = document.querySelector('[data-cart-form]');
if (cartForm) {
  const rupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value).replace(/\s/g, '');
  const refreshCart = () => {
    let total = 0;
    let count = 0;
    cartForm.querySelectorAll('[data-cart-item]').forEach(item => {
      const input = item.querySelector('.cart-quantity');
      const quantity = Math.max(1, Math.min(Number(input.max), Number(input.value) || 1));
      input.value = quantity;
      const subtotal = Number(item.dataset.price) * quantity;
      item.querySelector('[data-subtotal]').textContent = rupiah(subtotal);
      total += subtotal;
      count += quantity;
    });
    document.querySelector('[data-cart-total]').textContent = rupiah(total);
    document.querySelector('[data-cart-count]').textContent = count;
  };
  cartForm.addEventListener('click', event => {
    const button = event.target.closest('[data-qty-minus], [data-qty-plus]');
    if (!button) return;
    const input = button.closest('.quantity-control').querySelector('.cart-quantity');
    input.value = Number(input.value) + (button.hasAttribute('data-qty-plus') ? 1 : -1);
    refreshCart();
  });
  cartForm.addEventListener('input', event => {
    if (event.target.matches('.cart-quantity')) refreshCart();
  });
}
