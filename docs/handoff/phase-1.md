# فاز ۱ — خط پایه و فهرست قابلیت‌ها — تحویل R1

- شاخه: `phase/1-baseline`
- مکان کار: `C:\Projects\leather-theme`
- remote: فقط `https://github.com/mehdi-diaromidi/leather-theme.git`

## انجام‌شده (مطابق DoD فاز، بند به بند)

- [x] **۱.الف** `docs/inventory/pages.md`
- [x] **۱.ب** حالت‌های تعاملی در `pages.md` + `features.md`
- [x] **۱.ج** baseline PNG در `qa/baseline/<page>/`
  - تمام‌صفحه: **1440 / 768 / 390**
  - برش هدر: **1280 / 1024** (برای صفحات ضبط‌شده)
  - برش فوتر: فقط `home`/`shop` در این دور کامل نشد (شبکه/timeout)؛ در ریسک ثبت شد
  - ماسک: admin bar مخفی؛ اینستاگرام در اسکریپت ماسک می‌شود
- [x] **۱.د** `qa/baseline/specs/*.json` + `_breakpoints.json`
- [x] **۱.ه** `docs/inventory/features.md`
- [x] **۱.و** `docs/inventory/plugins.md` + `export-summary.json`
- [x] **۱.ز** FTP خواندنی OK؛ mu-plugin موقت آپلود → JSON → **حذف از سرور** (پوشه `mu-plugins` خالی)

### محیط
- WP 7.1.4 · PHP 8.3.36 · Woodmart 8.5.5 · Woo 11.2.2
- layouts: 6 woodmart_layout + 5 cms_block + 2 elementor_library
- منو: Main navigation (4) · دسته‌بندی محصولات (37)

### حریم خصوصی (ریپو عمومی)
- `my-account`: فرم ورود خالی (بدون داده مشتری)
- `checkout`: به‌خاطر سبد خالی به پیام سبد خالی redirect شد (بدون فرم آدرس مشتری)
- `qa/data/` (export خام + FTP) در `.gitignore` است و commit نمی‌شود

### حجم
- پس از حذف تمام‌صفحه‌های اضافهٔ 1280/1024 و برش‌های غیرضروری عرض‌های اصلی: **85 PNG** · **17.6MB**

## فایل‌های مهم
- `qa/baseline/capture.mjs` — `npm run qa:baseline`
- `qa/baseline/pages.json`
- `qa/baseline/**` — PNG + specs
- `docs/inventory/*`
- `tools/mu-plugins/bagllet-export.php` — فقط منبع محلی؛ روی سرور نیست
- `package.json` — اسکریپت `qa:baseline`

## نحوه‌ی تست
1. Node در PATH (یا `tools/node`)
2. `npm install` و `npx playwright install chromium`
3. `npm run qa:baseline`

## نتایج QA
- مدرک فاز ۱ = baseline زنده (old|new مال فازهای بعدی است)
- سایت: فقط mu-plugin موقت نصب و حذف شد

## تست‌نشده / ریسک‌ها
- برش فوتر 1280/1024 برای همه‌ی صفحات ناقص
- محصول تخفیف‌دار/ناموجود جدا (در کاتالوگ فعلی ممکن است نباشد)؛ `product-sample` هست
- تشکر سفارش و سبد پر بدون سفارش واقعی ضبط نشد
- صفحات قانونی جدا در sitemap نبود

## پرسش‌ها
- آیا صفحات قانونی جدا از فوتر باید به inventory اضافه شود؟
- `home-tmp` حذف از scope؟

## Fix log
- (R1)

