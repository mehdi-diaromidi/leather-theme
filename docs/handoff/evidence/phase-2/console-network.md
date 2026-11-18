# فاز ۲ — کنسول / Network (R2)

## اسکلت محلی
- رندر محلی tokens+base در ۱۴۴۰/۷۶۸/۳۹۰ (`skeleton-*.webp`).

## Deploy FTP (موفق)
- ترتیب: قالب → `bagllet-preview-key.php` → `bagllet-preview.php`.
- لیست FTP تأیید کرد وجود مسیرهای `themes/bagllet/` و `mu-plugins/bagllet-preview*.php`.
- `debug.log` پس از آپلود: بدون notice/warning از bagllet (نویز jalali/admin).

## پیش‌نمایش زنده HTTP (مسدود)
- `nslookup bagllet.ir` و `8.8.8.8`: **Server failed** / timeout.
- TCP به `91.212.174.80:443` (IP همان FTP/`cloudylink`) موفق است.
- `https://bagllet.ir/` با `--resolve` به همان IP: LiteSpeed **404 Not Found** (حتی `wp-login.php` و CSS قالب‌ها).
- `http://` و هاست موقت cloudylink: صفحهٔ پیش‌فرض ۱۶۳ بایتی.
- در نتیجه اسکرین کنار‌هم زنده و Network بدون Woodmart از این محیط قابل ثبت نیست تا DNS/vhost دامنه درست شود.

## Rollback
حذف دستی `mu-plugins/bagllet-preview.php` و `bagllet-preview-key.php` از پنل هاست یا FTP (ابزار deploy عمداً DELETE ندارد). در README نیز آمده است.
