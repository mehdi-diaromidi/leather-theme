# بازبینی فاز 0 — دور 1

نتیجه: ❌ نیازمند اصلاح (محتوای فنی فاز تقریباً درست است؛ مشکل اصلی «مکان کار» است)

بازبینی‌شده: شاخه‌ی `phase/0-repo` (tip `e4db3b6`) در ریپوی محلی `C:\Projects\bagllet`.

## یافته‌ها

### F1 [blocker] — کار در ریپو و پوشه‌ی اشتباه انجام شده
- محل: remote `origin` = `https://origin.cursor.com/.../bagllet.git` (ریپوی ساخته‌شده توسط Cursor)، پوشه‌ی `C:\Projects\bagllet`.
- مشکل: ریپوی مرجع پروژه `https://github.com/mehdi-diaromidi/leather-theme.git` است و پوشه‌ی کار `C:\Projects\leather-theme`. یک ریپوی تازه ساخته شد و شاخه به جای GitHub به یک remote دیگر push شد.
- راه رفع: کار را در `C:\Projects\leather-theme` انجام بده؛ قبل از هر push `git remote -v` را چک کن (باید فقط `github.com/mehdi-diaromidi/leather-theme` باشد). remote دوم نساز.
- راستی‌آزمایی: `git remote -v` و `git ls-remote origin` در handoff بیاید.

### F2 [blocker] — هویت commit
- مشکل: نویسنده‌ی commit ها `Mehdi <mehdi@bagllet.ir>` است؛ هویت صاحب ریپو نیست.
- راه رفع: همان هویتی که commit های `main` دارند: `git log -1 --format='%an <%ae>' main` را بخوان و با `git config user.name/user.email` (فقط local همین ریپو) تنظیم کن. هیچ نام ابزار/ایجنتی در commit یا فایل‌ها نیاید.

### F3 [blocker] — ۴۵۴ فایل، شامل دارایی‌های بی‌ربط و حجیم، track شده
- مشکل: `logo/` (169 فایل)، `design/` (157)، `logo edited/`، `col/`، `1.png..8.png`، `photo_*.jpg`، `bagllet-product-ui/` (نسخه‌ی قدیمی مستقل)، `assets/` ریشه و `.cursor/rules/document-patches-after-update.mdc` (مربوط به پروژه‌ی دیگر) وارد ریپو شده‌اند.
- راه رفع: در `leather-theme` فقط ساختار `public_html/wp-content/...`، `docs/`، `qa/`، `tools/`، فایل‌های ریشه‌ی لازم و شناسنامه‌ی استایل باشد. از پوشه‌ی `C:\Projects\bagllet` **هیچ دارایی‌ای کپی نکن** مگر با درخواست صریح در فاز بعدی (لوگو SVG در فاز 1 از مدیر گرفته می‌شود).
- راستی‌آزمایی: `git ls-files | wc -l` کمتر از ~100؛ بدون `logo/`، `design/`، تصویر ریشه.

### F4 [blocker] — فایل‌های ممنوعه در ریپو
- مشکل: `CLAUDE.md` و `docs/mehdi-skill-SKILL.md` track شده‌اند و README به `CLAUDE.md` لینک می‌دهد؛ ریپو نباید نام یا فایل‌های ابزار/ایجنت دیگری داشته باشد.
- راه رفع: وجود ندارند در `leather-theme`؛ README را هم بدون این لینک بنویس (توکن‌ها در `شناسنامه استایل bagllet.ir.md`).
- راستی‌آزمایی: `git grep -i -n -E "claude|anthropic"` خالی.

### F5 [major] — ساختار و اسناد بر پایه‌ی نسخه‌ی قدیمی نقشه‌راه
- مشکل: پوشه‌ها `themes/bagllet` و `plugin/...` ساخته شده‌اند؛ ساختار هم‌ریخت هاست `public_html/wp-content/themes/bagllet` و `.../plugins/bagllet-widgets` است. نسخه‌ی `docs/THEME-ROADMAP.md` داخل `bagllet` منسوخ است (بدون بخش‌های ۰.۱ ایمنی FTP، ۵.۱ راستی‌آزمایی بصری و معیار راست‌چین).
- راه رفع: روی `main` ریپوی `leather-theme` (به‌روز) شاخه بساز: `git pull origin main` سپس `git checkout -b phase/0-repo`. نقشه‌راه و پروتکل را از همان نسخه‌ی main بخوان، نه از `bagllet`.

## موارد تاییدشده (قابل استفاده‌ی مجدد)
- `.gitignore`: اسرار و `.vscode/sftp.json`/`deploy.ps1`/`.env*` را درست ignore می‌کند. (در `leather-theme` هم نسخه‌ی معادل هست؛ موارد مفیدِ اضافه‌ی نسخه‌ی تو — `.ftp.env.*`، `credentials*`، `*secrets*`، `desktop.ini` — را ادغام کن.)
- `.editorconfig` و `.gitattributes` (LF) مناسب‌اند؛ به `leather-theme` منتقل کن.
- بررسی اسرار: مقادیر FTP در HEAD و کل تاریخچه‌ی ریپوی تو **یافت نشد** (توسط ناظر با `git grep -F` روی همه‌ی commit ها تایید شد).
- جای‌نگهدارهای `docs/LICENSES.md` و `docs/PARITY-EXCEPTIONS.md` مناسب‌اند؛ فقط انکودینگ UTF-8 را حفظ کن (متن فارسی خراب نشود).
- گزارش handoff از نظر قالب و صداقت (مشخص‌کردن کارهای نشده) خوب بود.

## دستور بعدی به ایجنت (دور 2)
1. پنجره‌ی Cursor را روی `C:\Projects\leather-theme` باز کن. `git remote -v` باید فقط GitHub باشد.
2. `git pull origin main`؛ `git checkout -b phase/0-repo`.
3. این‌ها را اضافه/تکمیل کن (نه بیشتر): `.editorconfig`، `.gitattributes`، ادغام `.gitignore`، `docs/LICENSES.md`، `docs/PARITY-EXCEPTIONS.md`، ساختار خالی (با `.gitkeep`) برای `public_html/wp-content/themes/bagllet/{inc,template-parts,woocommerce,assets/{css,js,fonts,img,icons},languages}`، `qa/{baseline,diff,checks}`، `tools/`، به‌روزرسانی `README.md` و `docs/PROGRESS.md`.
4. `docs/handoff/phase-0.md` را برای R2 دوباره بنویس (شامل `git remote -v`، `git ls-files | wc -l`، خروجی grep ها).
5. push به GitHub روی شاخه‌ی `phase/0-repo` و توقف.

درباره‌ی پرسش‌های handoff: `design/` و PNG های ریشه **در ریپو نمانند** (ریپو فقط کد و مستندات). ورودی‌های فاز 1 (بخش ۲ نقشه‌راه) را مدیر طبق نسخه‌ی جدید فراهم می‌کند: دسترسی FTP در `.env.local`، لاگین در مرورگر داخلی Cursor.
