# Hostinger Yükleme - Hızlı Başlangıç

## 1️⃣ Database Oluştur
Hostinger Panel → Databases → MySQL → Create New Database
- Database adını not al
- Kullanıcı adını not al  
- Şifreyi not al

## 2️⃣ config.php Düzenle
`php-version/config.php` dosyasını aç ve Hostinger bilgilerini gir:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'Dogruhoca1');  // Hostinger'dan
define('DB_USER', 'Dogruhoca1');      // Hostinger'dan
define('DB_PASS', 'YourPassword123');       // Senin şifren
define('SITE_URL', 'https://yourdomain.com');
```

## 3️⃣ Dosyaları Yükle
`php-version` klasörünün **İÇİNDEKİ TÜM DOSYALARI** Hostinger'ın `public_html` klasörüne yükle.

**Doğru yapı:**
```
public_html/
├── index.php
├── config.php
├── login.php
├── register.php
├── dashboard.php
├── api/
└── static/
```

## 4️⃣ İlk Açılış
Tarayıcıda sitenizi açın: `https://yourdomain.com`
Database tabloları otomatik oluşturulacak.

## 5️⃣ Admin Kullanıcı Oluştur
1. `https://yourdomain.com/register.php` → Kayıt ol
2. **Öğretmen** seç
3. Kullanıcı adı: `AbdullahAdrainMorsy` veya `DogruHoca`
4. Şifre belirle ve kayıt ol

## 6️⃣ Admin Girişi
`https://yourdomain.com/admin-login.php` → Giriş yap

## ✅ Hazır!
Dashboard'dan içerik ekleyebilirsiniz.

---

**Sorun mu var?**
- Database bilgilerini kontrol et
- Dosyaların `public_html` içinde olduğundan emin ol
- Browser console'da hata mesajlarını kontrol et
