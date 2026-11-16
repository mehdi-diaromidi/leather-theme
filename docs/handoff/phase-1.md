# فاز ۱ — خط پایه و فهرست قابلیت‌ها — تحویل R3

- شاخه: `phase/1-baseline`
- remote: `mehdi-diaromidi/leather-theme`
- هویت: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- بازبینی قبلی: [phase-1-R2](../review/phase-1-R2.md)

## Fix log (R3 — فقط F10–F14)

| یافته | اقدام |
|---|---|
| F10 | مگامنو با hover+باز کردن پنل؛ جستجو با تایپ «کیف» + نتایج ایجکس؛ sticky: اسکرول + برش بالای viewport — روی زنده هدر نمی‌چسبد (`docs/inventory/sticky-header-note.md`) |
| F11 | mini-cart پس از add-to-cart واقعی؛ برش `.cart-widget-side` با آیتم/جمع/دکمه‌ها |
| F12 | hover/focus دکمهٔ ATC (+ category/checkout/nav/footer در `states/button-*` و `link-hover-*`) |
| F13 | دو کاشی «کیف دستی زنانه/مردانه» روی سایت زنده بدون تصویرند — ثبت در `docs/inventory/f13-missing-category-images.md` (محتوای سایت، نه capture) |
| F14 | سایه‌ها و transitionها بدون `transition:none` اندازه‌گیری و در `_tokens.json` ادغام شد؛ `fontUsage` برای فاز ۲ |

## راستی‌آزمایی چشمی + هش (قبل از commit)

| حالت | ابعاد | بایت | MD5 | مشاهده |
|---|---|---:|---|---|
| mega-menu-open | 1440×414 | 172236 | `0ade3ab587f6c637d9c74fd75931ceeb` | دراپ‌داون زنانه با اقلام منو باز است |
| search-open | 1440×581 | 171268 | `a2a829b240bfb07465b3fea2a46a1bee` | فیلد با «کیف» + لیست نتایج ایجکس |
| sticky-header | 1440×150 | 63880 | `4e21bca959c49f00d2efbf1d7ffe4af0` | بالای viewport بعد از اسکرول؛ هدر sticky ظاهر نشد |
| mini-cart-open | 340×900 | 25681 | `1214be38bfb52009ca8c22f5a8aefc20` | پنل سبد با کمربند مجلسی، جمع و دکمه‌های سبد/تسویه |

سه فایل مگامنو/جستجو/sticky دیگر هش یکسان ندارند.

## `_tokens.json` (F14)
- boxShadows: 8 مقدار
- transitions: 3 مقدار (غیر none)
- fontUsage: iranyekan + IRANSansXFaNum (هر دو تجاری → فاز ۲: Vazirmatn + آپلود)

## صفحات.md
وضعیت حالت‌ها با فایل واقعی هم‌تراز شد؛ sticky به‌عنوان «اسکرول ثبت شد / sticky غیرفعال روی زنده» علامت خورد.

## به مدیر
تصویر دو کاشی «کیف دستی زنانه» و «کیف دستی مردانه» در ویجت دسته‌بندی صفحه اصلی خالی است.

## پرسش‌ها
- ندارد (پاسخ لینکهای # از R2 اعمال شد).
