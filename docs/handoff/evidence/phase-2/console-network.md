# فاز ۲ — کنسول / Network (R3)

## دامنه
Staging زنده: `https://baglletleather.com/` (دامنه قبلی `bagllet.ir` دیگر برای تست استفاده نمی‌شود).

## بدون کلید
- خانه: HTTP 200؛ منابع از `/themes/woodmart/`.
- `wp-login.php`: HTTP 200.

## با کلید پیش‌نمایش
- ابتدا Fatal به‌خاطر تداخل نام `bagllet_widgets_init` با پلاگین `bagllet-widgets` → رفع با rename به `bagllet_theme_sidebars_init` (قالب 0.2.2).
- پس از hotfix: HTTP 200؛ CSS/JS از `/themes/bagllet/` (مثلاً `tokens.css?ver=0.2.2`)؛ صفر منبع `/themes/woodmart/`.
- مدرک سه‌عرضه: `preview-live-*.webp` در همین پوشه.

## debug.log
- Fatal پیش از hotfix در لاگ ثبت شد؛ پس از hotfix خط bagllet جدید دیده نشد (نویز قبلی jalali/woodmart/admin).

## Rollback
حذف دستی `mu-plugins/bagllet-preview.php` و `bagllet-preview-key.php` از پنل هاست یا FTP (ابزار deploy عمداً DELETE ندارد).
