# فاز ۲ — اسکلت قالب، توکن‌ها، ابزار QA — تحویل R2

- شاخه: `phase/2-skeleton`
- remote: `mehdi-diaromidi/leather-theme`
- هویت: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- بازبینی قبلی: [phase-2-R1](../review/phase-2-R1.md)

## Fix log (R2)

| یافته | اقدام |
|---|---|
| F1 | FTP OK: آپلود قالب سپس کلید سپس `bagllet-preview.php`. لیست FTP تأیید شد. `debug.log` بدون notice/warning مربوط به bagllet. **HTTP عمومی bagllet.ir از این محیط resolve نمی‌شود** (DNS fail روی 8.8.8.8؛ IP+SNI → صفحه پیش‌فرض هاست / گواهی منقضی) — مرورگر پیش‌نمایش زنده از اینجا ممکن نشد. Rollback در README. |
| F2 | `qa:capture:candidate`؛ `qa:diff --all` با آستانه 0.3٪ و fail روی عدم هم‌اندازگی؛ `qa:diff:specs` ±1px؛ کف نویز (دو کپی baseline): **0٪** |
| F3 | `docs/inventory/tokens-reconciliation.md` + بلوک `--live-*` در `tokens.css` |
| F4 | حذف `dir="rtl"` دستی از `header.php` |
| F5 | hover Clay فقط روی سطح تیره؛ `max-width` فقط `.bagllet-prose` |
| F6 | Requires ≥6.3، Tested up to 6.8، نسخه قالب 0.2.1 |
| F7 | `realpath` در `fonts.php`؛ حذف `fontFace` تکراری از `theme.json` (فقط `base.css`) |
| F8 | `deploy.mjs` با normalize/`..`، `--config -`، حذف `deploy.ps1`، `*.key` در gitignore، تلاش TLS |
| F9 | advisory scan برای bagllet-widgets؛ rgb/px؛ `qa:tokens-sync` |
| F10 | کوکی = HMAC؛ handle واقعی `bagllet-site` |

## انجام‌شده (DoD)

- [x] اسکلت + توکن (شناسنامه + parity live)
- [x] ابزار QA کامل‌تر (candidate/diff/specs/tokens-sync)
- [x] phpcs بدون error
- [x] چک ممنوعه‌ها صفر (advisory widgets: 222)
- [x] آپلود FTP قالب + پیش‌نمایش (تأیید list)
- [ ] اسکرین/Network زنده از پیش‌نمایش — **مسدود به‌خاطر DNS عمومی bagllet.ir**

## نتایج QA

### phpcs (خام)
```
.......... 10 / 10 (100%)
Time: 2.77 secs; Memory: 14MB
```
(فایل کامل: `qa/reports/phase-2/phpcs.txt` — ignore)

### debug.log
- قبل از آپلود در این محیط موجود نبود (0 بایت).
- بعد: `qa/reports/phase-2/debug-after.log` — خطوط مربوط به jalali-js / show_ui / MCP؛ **هیچ خط bagllet نیست**.

### FTP (تأیید روی سرور)
```
wp-content/themes/bagllet/ — style.css, functions.php, assets/, …
wp-content/mu-plugins/ — bagllet-preview.php, bagllet-preview-key.php
```

### Diff
| آزمون | نتیجه |
|---|---|
| کف نویز home/1440 (دو capture یکسان از baseline) | **0٪** PASS |
| diff پیش‌نمایش زنده vs baseline | **اجرا نشد** (DNS/HTTP عمومی) — انتظار درصد بالا پس از رفع DNS |

### توکن‌ها
جدول کامل: `docs/inventory/tokens-reconciliation.md`. `npm run qa:tokens-sync` → ok.

## مدرک بصری
- اسکلت محلی قبلی همچنان در `docs/handoff/evidence/phase-2/skeleton-*.webp`
- `console-network.md` به‌روز شد با وضعیت DNS/HTTP
- تصاویر live preview: در انتظار رفع DNS دامنه

## تست‌نشده / ریسک
- مشاهده مرورگر با کلید روی دامنه عمومی تا وقتی DNS/گواهی درست نشود
- HTML قدیمی bagllet-widgets ممکن است در پیش‌نمایش بماند (فاز ۳)

## پرسش‌ها
- DNS عمومی `bagllet.ir` از این محیط fail است؛ آیا رکورد A/NS اخیراً عوض شده؟ هاست FTP (`1540664912.cloudylink.com` → `91.212.174.80`) با Host/SNI برای `bagllet.ir` فقط LiteSpeed 404/صفحهٔ پیش‌فرض می‌دهد، نه وردپرس.
