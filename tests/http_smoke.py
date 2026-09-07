"""Pengujian HTTP pada server development. Membuat dan menghapus produk uji sendiri."""
import html as html_module
import os, re, json, uuid, base64, urllib.request, urllib.parse, urllib.error, http.cookiejar
BASE=os.environ.get('TEST_BASE_URL','http://localhost:8080').rstrip('/')
USER=os.environ.get('TEST_ADMIN_USERNAME','admin')
PASSWORD=os.environ.get('TEST_ADMIN_PASSWORD','BatikDemo!2026')
assert urllib.parse.urlparse(BASE).hostname in ('localhost','127.0.0.1'), 'Hanya server lokal.'
jar=http.cookiejar.CookieJar()
client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
checks=[]
def check(name,condition):
 assert condition,name
 checks.append(name);print('PASS:',name)
def request(path,data=None,file=None):
 headers={}
 if data is not None:
  if file:
   boundary='----BatikTest'+uuid.uuid4().hex;parts=[]
   for key,value in data.items():
    parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{value}\r\n'.encode())
   filename,mime,body=file
   parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="gambar"; filename="{filename}"\r\nContent-Type: {mime}\r\n\r\n'.encode()+body+b'\r\n')
   parts.append(f'--{boundary}--\r\n'.encode());body=b''.join(parts)
   headers['Content-Type']='multipart/form-data; boundary='+boundary
  else: body=urllib.parse.urlencode(data).encode()
 else: body=None
 try:r=client.open(urllib.request.Request(BASE+path,body,headers))
 except urllib.error.HTTPError as e:r=e
 text=r.read().decode('utf-8',errors='replace')
 return r.status,text,r.url,r.headers

def token(page):
 return re.search(r'name="csrf_test_name" value="([^"]+)"',page)[1]
def post(path,data,formpath='/admin/produk/tambah',file=None):
 _,html,_,_=request(formpath)
 return request(path,{'csrf_test_name':token(html),**data},file)
for path in ['/','/katalog','/tentang','/kontak','/admin/login']:
 status,html,_,_=request(path);check('GET '+path,status==200)
 for asset in set(re.findall(r'(?:src|href)="(http://localhost:8080/assets/[^\"]+)"',html)):
  check('asset '+asset.split('/assets/')[1],request(asset.replace(BASE,''))[0]==200)
for path in ['/admin','/admin/produk','/admin/produk/tambah','/admin/produk/edit/1']:
 check('admin terlindungi '+path,request(path)[2].endswith('/admin/login'))
check('CSRF wajib',request('/admin/login',{'username':USER,'password':PASSWORD})[0]==403)
status,html,_,_=post('/admin/login',{'username':USER,'password':'salah'},'/admin/login')
check('pesan login aman','Username atau password salah.' in html)
old_session=next(c.value for c in jar if c.name=='ci_session')
status,html,url,headers=post('/admin/login',{'username':USER,'password':PASSWORD},'/admin/login')
check('login',status==200 and url.endswith('/admin'))
check('session regenerasi',old_session!=next(c.value for c in jar if c.name=='ci_session'))
check('admin no-store','no-store' in headers.get('Cache-Control',''))
check('login sudah aktif dialihkan',request('/admin/login')[2].endswith('/admin'))
check('header nosniff',headers.get('X-Content-Type-Options')=='nosniff')
check('pencarian nama',len(re.findall('class="product-card',request('/katalog?q=Kawung')[1]))==2)
check('filter kategori',len(re.findall('class="product-card',request('/katalog?kategori=1')[1]))==2)
check('pencarian + filter',len(re.findall('class="product-card',request('/katalog?q=Kawung&kategori=1')[1]))==1)
check('hasil kosong','Produk tidak ditemukan' in request('/katalog?q=zzztidakada')[1])
check('SQL injection sebagai teks','Produk tidak ditemukan' in request('/katalog?q='+urllib.parse.quote("' OR 1=1 --"))[1])
check('XSS pencarian ter-escape','<script>alert(1)</script>' not in request('/katalog?q='+urllib.parse.quote('<script>alert(1)</script>'))[1])
check('input query array aman',request('/katalog?q[]=x&kategori[]=1')[0]==200)
check('detail tidak ditemukan',request('/katalog/tidak-ada')[0]==404)
check('WhatsApp placeholder nonaktif','Nomor WhatsApp toko belum diatur' in request('/katalog/kain-batik-kawung-sogan')[1])
name='Uji HTTP '+uuid.uuid4().hex[:8]
data={'nama_produk':name,'kategori_id':'1','harga':'123000','deskripsi':'Deskripsi produk untuk pengujian HTTP otomatis.','status_ketersediaan':'tersedia'}
_,html,url,_=post('/admin/produk/simpan',{**data,'harga':'-1'})
check('harga negatif ditolak',url.endswith('/tambah') and 'Periksa kembali' in html)
_,html,url,_=post('/admin/produk/simpan',{**data,'kategori_id':'999999'})
check('kategori palsu ditolak',url.endswith('/tambah') and 'Periksa kembali' in html)
_,html,url,_=post('/admin/produk/simpan',{**data,'nama_produk':'   '})
check('nama kosong ditolak',url.endswith('/tambah') and 'Periksa kembali' in html)
png=base64.b64decode('iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEElEQVQokWNgGAWjYBTAAAADEAABC3uRhAAAAABJRU5ErkJggg==')
for filename,mime,content in [('evil.svg','image/svg+xml',b'<svg xmlns="http://www.w3.org/2000/svg"/>'),('evil.php','image/png',png),('fake.jpg','image/jpeg',b'<?php echo 1; ?>'),('large.png','image/png',png+b'x'*(2*1024*1024))]:
 _,html,url,_=post('/admin/produk/simpan',data,file=(filename,mime,content))
 check('upload ditolak '+filename,url.endswith('/tambah') and 'Periksa kembali' in html)
created=[]
try:
 _,html,url,_=post('/admin/produk/simpan',data,file=('valid.png','image/png',png))
 check('tambah + upload valid',url.endswith('/admin/produk') and 'Produk berhasil disimpan' in html)
 row=re.search(r'<tr><td>.*?'+re.escape(name)+r'.*?</tr>',html)[0]
 pid=re.search(r'/admin/produk/edit/(\d+)',row)[1];created.append(pid)
 _,edit,_,_=request('/admin/produk/edit/'+pid)
 original=html_module.unescape(re.search(r'id="image-preview"[^>]*src="([^"]+)"',edit)[1])
 check('nama file acak',bool(re.search(r'/[a-f0-9]{32}\.png$',original)))
 data['nama_produk']=name+' Diubah';data['status_ketersediaan']='tidak_tersedia'
 _,html,url,_=post('/admin/produk/update/'+pid,data,'/admin/produk/edit/'+pid)
 check('edit tanpa gambar','Produk berhasil disimpan' in html)
 edit=request('/admin/produk/edit/'+pid)[1]
 check('gambar lama dipertahankan',original in html_module.unescape(edit))
 data['deskripsi']='<script>alert(1)</script> Deskripsi contoh panjang.'
 _,html,url,_=post('/admin/produk/update/'+pid,data,'/admin/produk/edit/'+pid,file=('replace.png','image/png',png))
 check('ganti gambar','Produk berhasil disimpan' in html)
 check('file lama dihapus',request(original.replace(BASE,''))[0]==404)
 catalog=request('/katalog?q='+urllib.parse.quote(name))[1]
 slug=re.search(r'/katalog/(uji-http-[^"/]+)',catalog)[1]
 detail=request('/katalog/'+slug)[1]
 check('detail dinamis + status',data['nama_produk'] in detail and 'Tidak tersedia' in detail)
 check('XSS deskripsi ter-escape','&lt;script&gt;' in detail and '<script>alert(1)</script>' not in detail)
 current=html_module.unescape(re.search(r'<img class="detail-image" src="([^"]+)"',detail)[1])
 check('GET hapus ditolak',request('/admin/produk/hapus/'+pid)[0]==404)
 _,html,_,_=post('/admin/produk/hapus/'+pid,{},'/admin/produk')
 created.remove(pid)
 check('hapus produk','Produk berhasil dihapus' in html and request('/katalog/'+slug)[0]==404)
 check('gambar ikut dihapus',request(current.replace(BASE,''))[0]==404)
 # Enough rows to exercise pagination, always clean up owned fixtures.
 for i in range(5):
  data['nama_produk']=name+' Pagination '+str(i)
  _,html,_,_=post('/admin/produk/simpan',data)
  row=re.search(r'<tr><td>.*?'+re.escape(data['nama_produk'])+r'.*?</tr>',html)[0]
  created.append(re.search(r'/admin/produk/edit/(\d+)',row)[1])
 check('pagination katalog', 'page=2' in html_module.unescape(request('/katalog')[1]) and len(re.findall('class="product-card',request('/katalog?page=2')[1]))==2)
 check('pagination admin','page=2' in html_module.unescape(request('/admin/produk')[1]))
finally:
 for pid in created:post('/admin/produk/hapus/'+pid,{},'/admin/produk')
_,html,url,_=post('/admin/logout',{},'/admin/produk')
check('logout',url.endswith('/admin/login'))
check('session logout tidak dapat akses admin',request('/admin')[2].endswith('/admin/login'))
print(json.dumps({'passed':len(checks)},indent=2))
