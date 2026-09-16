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
const preview = document.getElementById('image-preview');
const originalPreview = preview?.src;
let previewURL;
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
