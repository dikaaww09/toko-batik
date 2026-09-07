// Browser verification only; Node is not part of the application runtime.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const base = process.env.TEST_BASE_URL || 'http://localhost:8080';
assert(['localhost', '127.0.0.1'].includes(new URL(base).hostname));
(async () => {
 const browser = await chromium.launch({headless:true, ...(process.env.CHROMIUM_PATH ? {executablePath:process.env.CHROMIUM_PATH} : {})});
 const context = await browser.newContext();
 const page = await context.newPage();
 const errors=[]; const badResponses=[]; let checks=0;
 page.on('pageerror',e=>errors.push(e.message));
 page.on('console',m=>{if(m.type()==='error')errors.push(m.text())});
 page.on('response',r=>{if(r.status()>=400)badResponses.push(r.status()+' '+r.url())});
 const check=(condition,name)=>{assert(condition,name);checks++;console.log('PASS: '+name)};
 const paths=['/','/katalog','/tentang','/kontak','/katalog/kain-batik-kawung-sogan','/admin/login'];
 fs.mkdirSync('build/screenshots',{recursive:true});
 for(const width of [320,375,768,1024,1440]){
  await page.setViewportSize({width,height:900});
  for(const path of paths){
   await page.goto(base+path);await page.waitForLoadState('networkidle');
   check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'tanpa overflow '+width+' '+path);
   check(await page.locator('h1').isVisible(),'heading '+width+' '+path);
   check(await page.locator('img').evaluateAll(imgs=>imgs.every(i=>i.complete&&i.naturalWidth>0)),'gambar '+width+' '+path);
   if(path==='/')await page.screenshot({path:`build/screenshots/home-${width}.png`,fullPage:true});
  }
 }
 await page.setViewportSize({width:375,height:850});await page.goto(base+'/');
 check(!(await page.locator('#main-nav').isVisible()),'menu mobile awal tertutup');
 await page.getByRole('button',{name:'Buka navigasi'}).click();
 await page.locator('#main-nav').waitFor({state:'visible'});
 await page.locator('#main-nav').getByRole('link',{name:'Katalog',exact:true}).click();
 await page.waitForURL('**/katalog');
 check(!(await page.locator('#main-nav').isVisible()),'menu mobile tertutup setelah navigasi');
 await page.getByLabel('Cari nama produk').fill('Kawung');await page.getByLabel('Kategori',{exact:true}).selectOption('1');
 await page.getByRole('button',{name:'Tampilkan'}).click();await page.waitForLoadState('networkidle');
 check(await page.locator('.product-card').count()===1,'pencarian dan filter melalui form');
 await page.locator('.detail-link').click();await page.waitForLoadState('networkidle');
 check((await page.locator('h1').textContent()).includes('Kawung'),'navigasi detail');
 await page.goto(base+'/admin/login');
 await page.getByLabel('Username',{exact:true}).fill(process.env.TEST_ADMIN_USERNAME||'admin');
 await page.getByLabel('Password',{exact:true}).fill(process.env.TEST_ADMIN_PASSWORD||'BatikDemo!2026');
 await page.getByRole('button',{name:'Masuk',exact:true}).click();await page.waitForURL('**/admin');
 check(await page.getByRole('heading',{name:'Selamat datang, Admin.'}).isVisible(),'login browser');
 for(const width of [320,375,768,1024,1440]){
  await page.setViewportSize({width,height:900});
  for(const path of ['/admin','/admin/produk','/admin/produk/tambah','/admin/produk/edit/1']){
   await page.goto(base+path);await page.waitForLoadState('networkidle');
   check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'admin tanpa overflow '+width+' '+path);
  }
  await page.screenshot({path:`build/screenshots/admin-form-${width}.png`,fullPage:true});
 }
 await page.goto(base+'/admin/produk');
 page.once('dialog',async d=>{check(d.type()==='confirm','dialog konfirmasi hapus');await d.dismiss()});
 await page.getByRole('button',{name:'Hapus Kain Batik Kawung Sogan',exact:true}).click();
 check(await page.getByText('Kain Batik Kawung Sogan',{exact:true}).isVisible(),'batal hapus mempertahankan produk');
 await page.getByRole('link',{name:'+ Tambah Produk',exact:true}).click();
 await page.getByLabel('Nama produk',{exact:true}).fill('Uji Browser '+Date.now());
 await page.getByLabel('Harga (Rp)').fill('145000');
 await page.getByLabel('Kategori',{exact:true}).selectOption('1');
 await page.getByLabel('Deskripsi produk').fill('Produk sementara untuk pengujian browser otomatis.');
 await page.getByLabel('Unggah gambar (opsional)').setInputFiles({name:'test.png',mimeType:'image/png',buffer:Buffer.from('iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAADElEQVQokWNgGAWjAAABEAAAAVz4S38AAAAASUVORK5CYII=','base64')});
 check((await page.locator('#image-status').textContent()).includes('Pratinjau diperbarui'),'pratinjau unggahan');
 // HTTP suite verifies file persistence. Here test native form and delete dialog.
 await page.getByLabel('Unggah gambar (opsional)').setInputFiles([]);
 await page.getByRole('button',{name:'Simpan Produk'}).click();await page.waitForURL('**/admin/produk');
 check(await page.getByRole('status').isVisible(),'tambah produk browser');
 const row=page.locator('tr').filter({hasText:'Uji Browser'}).first();
 await row.getByRole('link',{name:/Edit/}).click();
 await page.getByLabel('Harga (Rp)').fill('155000');
 await page.getByRole('button',{name:'Simpan Produk'}).click();await page.waitForURL('**/admin/produk');
 check(await page.locator('tr').filter({hasText:'Uji Browser'}).getByText('Rp155.000').isVisible(),'edit browser');
 page.once('dialog',async d=>d.accept());
 await page.locator('tr').filter({hasText:'Uji Browser'}).getByRole('button',{name:/Hapus/}).click();
 await page.waitForLoadState('networkidle');
 check(await page.locator('tr').filter({hasText:'Uji Browser'}).count()===0,'hapus browser');
 await page.getByRole('button',{name:'Keluar'}).click();await page.waitForURL('**/admin/login');
 check(true,'logout browser');
 await page.emulateMedia({reducedMotion:'reduce'});await page.goto(base+'/');
 check(await page.locator('.product-visual img').first().evaluate(el=>getComputedStyle(el).transitionDuration==='0s'),'prefers-reduced-motion');
 await page.keyboard.press('Tab');
 check(await page.locator('.skip-link').evaluate(el=>el===document.activeElement),'skip link keyboard');
 check(errors.length===0,'console tanpa error: '+JSON.stringify(errors));
 check(badResponses.length===0,'request tanpa 404: '+JSON.stringify(badResponses));
 console.log(JSON.stringify({passed:checks,errors,badResponses}));
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
