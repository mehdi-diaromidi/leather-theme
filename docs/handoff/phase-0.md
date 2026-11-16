# تحویل فاز ۰ — ایمنی ریپو و ساختار (R2)

شاخه: `phase/0-repo`
مکان کار: `C:\Projects\leather-theme`
دور: اصلاح یافته‌های [phase-0-R1](../review/phase-0-R1.md)

## انجام‌شده

### رفع F1 — مکان و remote
- کار فقط در `C:\Projects\leather-theme`.
- `git remote -v`:
  ```
  origin  https://github.com/mehdi-diaromidi/leather-theme.git (fetch)
  origin  https://github.com/mehdi-diaromidi/leather-theme.git (push)
  ```
- remote دوم ساخته نشد.
- `git ls-remote --heads origin` (قبل از push این شاخه): فقط `refs/heads/main` روی همان GitHub.

### رفع F2 — هویت commit
- هویت `main`: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- `git config --local user.name/user.email` روی همین ریپو به همان مقدار تنظیم شد.
- نام ابزار/ایجنت در commit یا فایل‌های جدید نیست.

### رفع F3 — بدون دارایی بی‌ربط
- از `C:\Projects\bagllet` هیچ `logo/`، `design/`، PNG ریشه، `col/`، `bagllet-product-ui/` کپی نشد.
- فقط ساختار خالی قالب + اسناد فاز ۰.
- شمارش فایل‌های track‌شده پس از commit این دور در انتهای همین فایل آمده است (هدف: کمتر از ~۱۰۰).

### رفع F4 — بدون فایل ممنوعه
- فایل‌های راهنمای ابزار دیگر و فایل اسکیل شخصی در ریپو وجود ندارند.
- README بدون لینک به فایل ابزار دیگر؛ توکن‌ها فقط از `شناسنامه استایل bagllet.ir.md`.
- جست‌وجوی نام ابزارهای ایجنت در کل ریپو (به‌جز فایل‌های بازبینی) → خالی.

### رفع F5 — ساختار هم‌ریخت هاست + نقشه‌راه به‌روز
- پایه: `main` به‌روز (`92f1f7f`) سپس `phase/0-repo`.
- نقشه‌راه همان نسخه‌ی `main` (با بخش‌های ۰.۱، ۵.۱ و …).
- ساختار:
  - `public_html/wp-content/themes/bagllet/{inc,template-parts,woocommerce,assets/{css,js,fonts,img,icons},languages}/.gitkeep`
  - `qa/{baseline,diff,checks}/.gitkeep`
  - `tools/.gitkeep`
  - پلاگین موجود `plugins/bagllet-widgets/` دست نخورده ماند.

### فایل‌های فاز ۰
- `.editorconfig`، `.gitattributes` (LF)
- `.gitignore` ادغام‌شده (`.ftp.env.*`، `credentials*`، `*secrets*`، `desktop.ini` + موارد قبلی leather-theme)
- `docs/LICENSES.md`، `docs/PARITY-EXCEPTIONS.md` (جای‌نگهدار UTF-8)
- `README.md` و `docs/PROGRESS.md` به‌روز

## انجام‌نشده (عمداً خارج از فاز ۰)
- محتوای قالب (`style.css`، `functions.php`، …) → فاز ۲
- baseline / Playwright → فاز ۱
- لوگو SVG و دارایی‌های طراحی → طبق نقشه‌راه، ورودی مدیر در فاز ۱
- `tools/deploy.ps1` واقعی → فاز ۲ (و در `.gitignore` است)

## راستی‌آزمایی اسرار
- الگوی DoD نقشه‌راه روی اسناد/ریشه اجرا شد؛ مقدار واقعی FTP/رمز یافت نشد.
- `.env.example` فقط قالب است؛ `.env.local` ignore است.

## پرسش برای ناظر
- هیچ ابهام باز برای ورود به بازبینی R2 نیست. پس از تایید، فاز ۱ با FTP در `.env.local` و لاگین مرورگر داخلی شروع می‌شود.

## شمارش فایل (پس از staging/commit این دور)
**74** فایل (git ls-files).
