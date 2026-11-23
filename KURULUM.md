# Kurulum

## Gereksinimler

- Git ve Composer 2
- PHP 8.4.1 veya üzeri
- PHP uzantıları arasında `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo` ve `zip` etkin olmalı

Projenin arayüz dosyaları depoda hazırdır. Node.js kurulumu gerekmez.

## Windows PowerShell

İlk kurulumda aşağıdaki komutları sırayla çalıştırın. `php -v` ve `composer --version` çıktıları doğru PHP sürümünü göstermeli.

```powershell
git clone https://github.com/tufanakbas23/kitap-kosesi.git
cd kitap-kosesi
php -v
composer --version
composer install --no-dev
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Path database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

macOS veya Linux kullanıyorsanız `Copy-Item` yerine `cp .env.example .env`, `New-Item` yerine `touch database/database.sqlite` çalıştırın.

- [Mağazayı açın](http://127.0.0.1:8000/)
- [Yönetim panelini açın](http://127.0.0.1:8000/admin)

Sonraki açılışlarda, proje klasöründen `php artisan serve --host=127.0.0.1 --port=8000` komutunu çalıştırmanız yeterli. `storage:link` komutu yönetici panelinden yüklenen kitap kapaklarını tarayıcıda göstermek için kullanılır.

## Demo hesapları

| Rol | E-posta | Şifre |
| --- | --- | --- |
| Müşteri | `test@kitapkosesi.test` | `123456` |
| Yönetici | `admin@kitapkosesi.test` | `admin123` |

Demo ödeme için kart numarası `4111 1111 1111 1111`, güvenlik kodu `123` ve gelecekteki bir son kullanma tarihi kullanın. Geçerli indirim kuponu `HOSGELDIN10`. Süresi geçmiş kupon senaryosu için `YAZ2025` kullanılabilir.
