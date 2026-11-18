# فاز ۲ — اسکلت قالب، توکن‌ها، ابزار QA — تحویل R3

- شاخه: `phase/2-skeleton`
- remote: `mehdi-diaromidi/leather-theme`
- هویت: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- بازبینی قبلی: [phase-2-R1](../review/phase-2-R1.md) · [phase-2-R2](../review/phase-2-R2.md)
- Staging زنده: `https://baglletleather.com/`

## Fix log (R3)

| یافته | اقدام |
|---|---|
| ترتیب پس از رفع دامنه | دامنه به `baglletleather.com`؛ بدون کلید = Woodmart؛ با کلید = قالب bagllet؛ login سالم |
| Fatal پیش‌نمایش | تداخل `bagllet_widgets_init` با پلاگین → `bagllet_theme_sidebars_init`؛ نسخه قالب **0.2.2** |
| N1 | نام هاست FTP و IP از handoff/evidence حذف شد (`<ftp-host>` / `<server-ip>` در صورت نیاز) |
| N2 | کف نویز از **دو capture زنده Woodmart**؛ توضیح `--noise-floor` اصلاح شد |

## Fix log (R2 — خلاصه)

F2–F10 در دور قبل در کد؛ F1 اکنون با تست زنده تکمیل شد.

## انجام‌شده (DoD)

- [x] اسکلت + توکن + ابزار QA
- [x] phpcs بدون error
- [x] آپلود FTP + mu-plugin پیش‌نمایش
- [x] بدون کلید: Woodmart؛ با کلید: bagllet، بدون منبع `/themes/woodmart/`
- [x] کف نویز زنده + diff پیش‌نمایش
- [x] مدرک سه‌عرضه `preview-live-*.webp`

## نتایج QA

### phpcs
```
.......... 10 / 10 (100%)
```

### debug.log
- Fatal قبل از hotfix: `Cannot redeclare bagllet_widgets_init()` (ثبت شد).
- پس از hotfix: بدون Fatal/notice جدید از bagllet.

### بدون کلید / با کلید
| حالت | نتیجه |
|---|---|
| `/` بدون کلید | 200، `/themes/woodmart/` |
| `/?bagllet_preview=…` | 200، `/themes/bagllet/`، `hasWoodmart=false` |
| `wp-login.php` | 200 |

### Diff
| آزمون | نتیجه |
|---|---|
| کف نویز زنده home/1440 (noise-a vs noise-b) | **0.041٪** PASS |
| `qa:diff --all` روی پیش‌نمایش home | **size-mismatch FAIL** (ارتفاع اسکلت کمتر از baseline — انتظار فاز ۲) |

### توکن‌ها
`docs/inventory/tokens-reconciliation.md`؛ `qa:tokens-sync` ok.

## مدرک بصری
- `docs/handoff/evidence/phase-2/preview-live-{1440,768,390}.webp`
- `console-network.md` به‌روز (R3)
- اسکلت محلی قبلی: `skeleton-*.webp`

## تست‌نشده / ریسک
- HTML قدیمی bagllet-widgets ممکن است در پیش‌نمایش بماند (فاز ۳)
- مرورگر داخلی Cursor پشت پروکسی سیستم timeout می‌دهد؛ تست با Playwright مستقیم/`curl` انجام شد

## پرسش‌ها
- آیا `SITE_URL` و لینک‌های عملیاتی همه جا به `baglletleather.com` به‌روز شوند (نقشه‌راه/شناسنامه) یا فقط tooling کافی است؟
