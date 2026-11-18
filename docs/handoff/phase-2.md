# فاز ۲ — اسکلت قالب، توکن‌ها، ابزار QA — تحویل R1

- شاخه: `phase/2-skeleton`
- remote: `mehdi-diaromidi/leather-theme`
- هویت: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- پیش‌نیاز: `docs/review/phase-1-APPROVED.md` روی main

## انجام‌شده (DoD)

- [x] قالب معتبر در `public_html/wp-content/themes/bagllet/` (style.css، functions.php ماژولار، theme.json، screenshot موقت، قالب‌های پایه)
- [x] `assets/css/tokens.css` + `base.css` از شناسنامه (رنگ، تایپ 56/80…، radius 6–12، فاصله 4px، کانتینر 1280، گرید gap 24، فوکوس مشکی/Clay، RTL)
- [x] فونت: Vazirmatn bundled (OFL) + `@font-face` پویا برای آپلود سفارشی (`inc/fonts.php`)؛ letter-spacing 0؛ ارقام فارسی helper
- [x] ابزار QA: `qa/checks/forbidden.mjs`، `qa/diff/compare.mjs` (pixelmatch)، `phpcs.xml` + WPCS، اسکریپت‌های npm
- [x] ابزارها: Node portable موجود؛ PHP 8.2.26 و Composer در `tools/php` (ignore) نصب و در README ثبت شد
- [x] deploy: `tools/deploy.mjs` (+ `tools/deploy.ps1`) — مسیرهای مجاز، backup، بدون DELETE، `--dry-run`
- [x] mu-plugin پیش‌نمایش `bagllet-preview.php` + مثال کلید؛ کلید واقعی فقط از `.env.local` → `bagllet-preview-key.php` (gitignore)
- [x] چک ممنوعه‌ها = 0؛ phpcs بدون error؛ `qa:diff --self-test` = 0 mismatch
- [ ] پیش‌نمایش زنده روی bagllet.ir — **تست‌نشده به‌خاطر FTP 530** (اسکریپت آماده؛ نیاز به تمدید دسترسی)

## فایل‌های مهم

- `public_html/wp-content/themes/bagllet/**` — اسکلت قالب
- `public_html/wp-content/mu-plugins/bagllet-preview.php` — سوییچ پیش‌نمایش
- `tools/deploy.mjs` / `tools/deploy.ps1` — آپلود امن
- `qa/checks/forbidden.mjs`، `qa/diff/compare.mjs`، `phpcs.xml`، `composer.json`
- `docs/handoff/evidence/phase-2/` — رندر محلی اسکلت

## نحوه‌ی تست

1. `npm run qa:checks` → ok/count 0
2. `npm run qa:diff:self` → mismatched 0
3. `tools/php/php.exe vendor/bin/phpcs --standard=phpcs.xml` → بدون error
4. `node tools/deploy.mjs --dry-run` → فهرست PUT بدون آپلود
5. پیش‌نمایش زنده (پس از رفع FTP): آپلود با `node tools/deploy.mjs` سپس `/?bagllet_preview=<KEY>` — بازدیدکنندهٔ بدون کلید همچنان Woodmart می‌بیند

## نتایج QA

- مدرک بصری: `docs/handoff/evidence/phase-2/skeleton-{1440,768,390}.webp` + `console-network.md`
- phpcs: clean (10 فایل)
- چک ممنوعه‌ها: 0
- diff self-test home/1440: 0٪

## تست‌نشده / ریسک‌ها

- آپلود FTP به سرور: `curl (67) Access denied: 530` — اعتبار فعلی کار نمی‌کند؛ بدون آپلود، پیش‌نمایش روی staging راستی‌آزمایی نشد
- قالب روی وردپرس تمیز محلی (بدون هسته WP در این ریپو) اجرا نشد؛ فقط `php -l` + ساختار + CSS
- dequeue هندل‌های legacy bagllet-widgets در پیش‌نمایش حدسی است تا فازهای بعد دقیق‌تر شود

## پرسش‌ها از مدیر / ناظر

- لطفاً FTP را تمدید/درست کنید تا deploy و تست پیش‌نمایش زنده انجام شود.
- آیا مسیر `FTP_ROOT=/public_html` هنوز درست است؟
