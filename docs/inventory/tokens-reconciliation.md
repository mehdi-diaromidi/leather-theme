# تطبیق توکن‌ها: شناسنامه ↔ ظاهر زنده (فاز ۲ / R2)

منبع اندازه‌گیری: `qa/baseline/specs/_tokens.json` و اسپک‌های صفحه. قاعده: **parity سایت زنده بر شناسنامه مقدم است**؛ مغایرت‌های عمدی برای تصمیم مدیر در فاز ۸.

## رنگ‌ها

| مقدار زنده | توکن شناسنامه / parity | یادداشت |
|---|---|---|
| `#000000` rgb(0,0,0) | `--color-black` | برابر |
| `#FFFFFF` rgb(255,255,255) | `--color-white` | برابر |
| `#C38D6A` rgb(195,141,106) | `--color-clay` | برابر |
| `#CEB89B` rgb(206,184,155) | `--color-sand` | برابر |
| `#F2F2F2` rgb(242,242,242) | `--color-gray-100` | برابر |
| `#6B7280` rgb(107,114,128) | `--color-text-muted` | برابر |
| `#4B5563` rgb(75,85,99) | `--color-text-muted-ivory` | برابر |
| `rgba(0,0,0,0)` | — | شفاف؛ توکن لازم نیست |
| `#AC9A83` rgb(172,154,131) | `--live-color-atc` | دکمه افزودن به سبد زنده (نزدیک Clay اما متفاوت) |
| `#777777` rgb(119,119,119) | `--live-color-gray-119` | متن ثانویه زنده |
| `#474747` rgb(71,71,71) | `--live-color-gray-71` | متن |
| `#333333` rgb(51,51,51) | `--live-color-text-51` | متن |
| `#52647A` rgb(82,100,122) | `--live-color-navy-82-100-122` | فقط زنده |
| `#E0B252` rgb(224,178,82) | `--live-color-gold-224-178-82` | فقط زنده |
| سایر خاکستری‌ها (10,36,38,88,91,102,118…) | نزدیک black/elevated/muted | در صورت نیاز فاز ۳ map می‌شوند |

### مغایرت برای تصمیم مدیر (فاز ۸)
- دکمه ATC زنده: پس‌زمینه `--live-color-atc` + **متن سفید** — شناسنامه می‌گوید روی Clay متن همیشه مشکی است. برای parity فعلی از `--live-*` استفاده می‌شود تا فاز ۸.

## Radius

| زنده | شناسنامه | parity |
|---|---|---|
| 0px | — | پیش‌فرض مرورگر |
| 6px | `--radius-sm` | برابر |
| 8px | `--radius-md` | برابر |
| 10px | `--radius-lg` | برابر |
| 12px | `--radius-xl` | برابر |
| 5px | — | `--live-radius-5` |
| 9px | — | `--live-radius-9` |
| 35px | — (پیل ممنوع در شناسنامه) | `--live-radius-pillish` فقط برای parity |

## سایه / transition / فونت

| زنده | توکن |
|---|---|
| dropdown `0 16px 40px rgba(0,0,0,.12)` | `--live-shadow-dropdown` |
| modal `0 20px 50px rgba(0,0,0,.5)` | `--live-shadow-modal` |
| badge `0 0 4px rgba(0,0,0,.17)` | `--live-shadow-badge` |
| `cubic-bezier(0.19, 1, 0.22, 1)` | `--live-ease-wood` (شناسنامه: `--ease-out` = 0.2,0.8,0.2,1) |
| iranyekan / IRANSansXFaNum | `--live-font-*` — تجاری؛ zip فروش = Vazirmatn |

## پوشش
جدول بالا همهٔ رنگ‌ها و radiusهای یکتای `_tokens.json` را پوشش می‌دهد (شفاف نادیده). چک `npm run qa:checks` باید سبز بماند.
