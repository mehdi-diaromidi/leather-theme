# سرریز افقی (فاز ۱ / R2)

## یافته
- پس از `overflow-x: clip` و مخفی‌کردن `.wd-skip-links`، `documentElement.scrollWidth` با viewport برابر است (home / products-categories در ۱۴۴۰/۷۶۸/۳۹۰).
- اختلاف عرض قبلی در PNGها (مثلاً ۱۴۴۰→۱۴۶۰) **آرتیفکت Playwright `fullPage`** بود، نه سرریز پایدار layout؛ در R2 با `shotFull` مبتنی بر viewport به ارتفاع کامل، عرض فایل = عرض هدف.
- `.wd-skip-links` خارج از صفحه (`right` بسیار بزرگ) است؛ در capture مخفی می‌شود.
- در برش فوتر بدون clip، `getBoundingClientRect().width` گاهی حدود `viewport − ۱۵` است (جعبه محتوا/`footer.bgf`)؛ فایل PNG با clip به عرض دقیق ۱۰۲۴/۱۲۸۰ ذخیره می‌شود.

## اثر روی فازهای بعد
diff بصری باید روی فایل‌های هم‌عرض باشد. اگر در فاز ۳ سرریز واقعی دیده شد، اینجا به‌روز شود.
