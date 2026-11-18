# bagllet — قالب اختصاصی وردپرس

بازسازی ظاهر و رفتار سایت [bagllet.ir](https://bagllet.ir) به‌صورت قالب وردپرس مستقل (بدون Woodmart)، قابل فروش در بازارهای فارسی‌زبان.

## ساختار (هم‌ریخت با هاست)

```
public_html/wp-content/
  themes/bagllet/            قالب جدید (در حال ساخت)
  plugins/bagllet-widgets/   پلاگین همراه (ویجت‌های المنتور، صفحات درباره/تماس)
  mu-plugins/                فقط توسعه (پیش‌نمایش؛ در zip فروش نیست)
qa/                          baseline، diff، چک‌های خودکار
tools/                       اسکریپت‌های کمکی (بدون اسرار)
docs/                        نقشه‌راه، پروتکل بازبینی، handoff، review
شناسنامه استایل bagllet.ir.md   توکن‌های طراحی (رنگ، فونت، مقیاس‌ها)
```

## اسناد

| فایل | نقش |
|---|---|
| [docs/THEME-ROADMAP.md](docs/THEME-ROADMAP.md) | منبع حقیقت — فازها، DoD، قواعد |
| [docs/REVIEW-PROTOCOL.md](docs/REVIEW-PROTOCOL.md) | چرخه‌ی تحویل و بازبینی |
| [docs/PROGRESS.md](docs/PROGRESS.md) | وضعیت فازها |
| [شناسنامه استایل bagllet.ir.md](شناسنامه%20استایل%20bagllet.ir.md) | توکن‌های طراحی |
| [docs/LICENSES.md](docs/LICENSES.md) | لایسنس دارایی‌ها |
| [docs/PARITY-EXCEPTIONS.md](docs/PARITY-EXCEPTIONS.md) | استثناهای parity (با تایید مدیر) |

## قواعد کوتاه

- ظاهر = سایت زنده (`qa/baseline`). اختلاف = باگ مگر در `PARITY-EXCEPTIONS.md`.
- هیچ کد/کلاس Woodmart (`wd-` / `xts-` / `woodmart`) در خروجی نیست.
- رنگ/فونت/فاصله فقط از CSS variables (`tokens.css`).
- اسرار (FTP، رمز، `.env`) هرگز در ریپو نیست.
- هر فاز = یک شاخه `phase/N-…` + `docs/handoff/phase-N.md` + توقف تا تایید.

## الزامات هدف

- PHP 7.4–8.3، آخرین وردپرس و ووکامرس
- RTL / فارسی / i18n (`bagllet`)
- فونت بسته‌ی فروش: Vazirmatn (OFL)؛ IRANYekan فقط از طریق آپلود سفارشی روی سایت bagllet.ir

## ابزار توسعه
- Node LTS (این محیط: `tools/node` portable، ignore) + Playwright
- PHP 8.2.26 portable در `tools/php` (ignore) + Composer phar — برای `phpcs`
- `npm run qa:baseline` — اسکرین‌شات و specs از سایت زنده
- `npm run qa:checks` — ممنوعه‌های Woodmart / hex / !important
- `npm run qa:diff:self` — خودآزمایی pixelmatch
- `npm run qa:phpcs` — WPCS روی قالب و mu-plugin پیش‌نمایش
- `npm run deploy:dry` / `npm run deploy` — آپلود امن FTP (اسرار فقط `.env.local`)

## شروع

1. `docs/THEME-ROADMAP.md` را کامل بخوان.
2. روند تحویل/بازبینی: `docs/REVIEW-PROTOCOL.md`.
3. وضعیت فازها: `docs/PROGRESS.md`.
