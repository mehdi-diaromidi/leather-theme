# فاز ۱ — خط پایه و فهرست قابلیت‌ها — تحویل R2

- شاخه: `phase/1-baseline`
- مکان: `C:\\Projects\\leather-theme`
- remote: فقط GitHub `mehdi-diaromidi/leather-theme`
- هویت commit: `mehdi-diaromidi <mehdidiaromidi@gmail.com>`
- بازبینی قبلی: [phase-1-R1](../review/phase-1-R1.md)

## Fix log (R2)

| یافته | اقدام |
|---|---|
| F1 | `preparePage`: اسکرول تدریجی، eager کردن lazy، انتظار `img` مرئی `complete`، `reducedMotion` + `animations:disabled`، توقف swiper؛ **incompleteSum = 0** |
| F2 | آرتیفکت `fullPage` رفع شد (`shotFull` با viewport به ارتفاع کامل). عرض همهٔ PNGهای تمام‌صفحه و header/footer دقیق است. جزئیات: `docs/inventory/overflow.md` |
| F3 | `npm run qa:states` → ۱۳ حالت در `qa/baseline/states/<id>/` + specs؛ جدول در `pages.md` |
| F4 | مهمان: `cart-full` + `checkout` فرم؛ `checkout-validation`؛ thankyou → فاز ۷ |
| F5 | `qa/baseline-private/` (gitignore) + `npm run qa:login` واقعی؛ فایل‌های محلی در همین گزارش |
| F6 | ۳۱ انتخابگر در specs؛ خلاصه در `qa/baseline/specs/_tokens.json` (۷۰ فایل اسپک) |
| F7 | `products-categories`؛ قوانین/همکاری `href="#"`؛ وبلاگ بدون نوشته؛ فوتر واقعی `footer.bgf` (نه `wd-footer` خالی)؛ برش footer+header در ۱۲۸۰/۱۰۲۴ |
| F8 | نسخه‌ها از `plugins.md`/`export-summary` عمومی حذف؛ کامل در `qa/data/` |
| F9 | `package.json`: `private:true`، description کوتاه، license حذف |

## انجام‌شده (DoD)

- [x] inventory pages/features/plugins/overflow
- [x] baseline عمومی با lazy-load fix و عرض دقیق
- [x] states تعاملی (۱۳ فایل)
- [x] cart-full / checkout مهمان
- [x] baseline-private محلی (gitignore)
- [x] specs + `_tokens.json`
- [x] `npm run qa:baseline` / `qa:states` / `qa:login`

## نتایج اندازه‌گیری

- تصاویر ناقص (incomplete مرئی): **0**
- PNG تمام‌صفحه: **57** — همه با عرض دقیق هدف
- برش header/footer: **24** — عرض دقیق ۱۰۲۴/۱۲۸۰
- حالت‌های تعاملی: **13** (برش المان؛ عرض فایل = عرض المان، نه viewport)

### جدول ابعاد — تمام‌صفحه

| فایل | عرض | ارتفاع |
|---|---:|---:|
| `404/1440.png` | 1440 | 1225 |
| `404/390.png` | 390 | 1870 |
| `404/768.png` | 768 | 1499 |
| `about/1440.png` | 1440 | 2851 |
| `about/390.png` | 390 | 4461 |
| `about/768.png` | 768 | 3337 |
| `cart-empty/1440.png` | 1440 | 1212 |
| `cart-empty/390.png` | 390 | 1870 |
| `cart-empty/768.png` | 768 | 1499 |
| `cart-full/1440.png` | 1440 | 1308 |
| `cart-full/390.png` | 390 | 2417 |
| `cart-full/768.png` | 768 | 2202 |
| `cat-mens-bag/1440.png` | 1440 | 1648 |
| `cat-mens-bag/390.png` | 390 | 2288 |
| `cat-mens-bag/768.png` | 768 | 2080 |
| `cat-mens/1440.png` | 1440 | 1648 |
| `cat-mens/390.png` | 390 | 2288 |
| `cat-mens/768.png` | 768 | 2080 |
| `cat-womens-bag/1440.png` | 1440 | 1648 |
| `cat-womens-bag/390.png` | 390 | 2288 |
| `cat-womens-bag/768.png` | 768 | 2080 |
| `cat-womens/1440.png` | 1440 | 1648 |
| `cat-womens/390.png` | 390 | 2288 |
| `cat-womens/768.png` | 768 | 2080 |
| `checkout/1440.png` | 1440 | 2327 |
| `checkout/390.png` | 390 | 4045 |
| `checkout/768.png` | 768 | 2756 |
| `compare/1440.png` | 1440 | 1182 |
| `compare/390.png` | 390 | 1870 |
| `compare/768.png` | 768 | 1499 |
| `contact/1440.png` | 1440 | 2356 |
| `contact/390.png` | 390 | 3615 |
| `contact/768.png` | 768 | 2883 |
| `home/1440.png` | 1440 | 4696 |
| `home/390.png` | 390 | 6772 |
| `home/768.png` | 768 | 6988 |
| `my-account/1440.png` | 1440 | 900 |
| `my-account/390.png` | 390 | 900 |
| `my-account/768.png` | 768 | 900 |
| `product-sample/1440.png` | 1440 | 3742 |
| `product-sample/390.png` | 390 | 5300 |
| `product-sample/768.png` | 768 | 4237 |
| `products-categories/1440.png` | 1440 | 7099 |
| `products-categories/390.png` | 390 | 8361 |
| `products-categories/768.png` | 768 | 12103 |
| `search-empty/1440.png` | 1440 | 1182 |
| `search-empty/390.png` | 390 | 1870 |
| `search-empty/768.png` | 768 | 1499 |
| `search-results/1440.png` | 1440 | 1648 |
| `search-results/390.png` | 390 | 2641 |
| `search-results/768.png` | 768 | 2470 |
| `shop/1440.png` | 1440 | 1648 |
| `shop/390.png` | 390 | 2597 |
| `shop/768.png` | 768 | 2470 |
| `wishlist/1440.png` | 1440 | 1182 |
| `wishlist/390.png` | 390 | 1870 |
| `wishlist/768.png` | 768 | 1499 |

### برش header / footer

| فایل | عرض | ارتفاع |
|---|---:|---:|
| `about/footer-1024.png` | 1024 | 600 |
| `about/footer-1280.png` | 1280 | 600 |
| `about/header-1024.png` | 1024 | 70 |
| `about/header-1280.png` | 1280 | 132 |
| `contact/footer-1024.png` | 1024 | 600 |
| `contact/footer-1280.png` | 1280 | 600 |
| `contact/header-1024.png` | 1024 | 70 |
| `contact/header-1280.png` | 1280 | 132 |
| `home/footer-1024.png` | 1024 | 600 |
| `home/footer-1280.png` | 1280 | 600 |
| `home/header-1024.png` | 1024 | 70 |
| `home/header-1280.png` | 1280 | 132 |
| `product-sample/footer-1024.png` | 1024 | 600 |
| `product-sample/footer-1280.png` | 1280 | 600 |
| `product-sample/header-1024.png` | 1024 | 70 |
| `product-sample/header-1280.png` | 1280 | 132 |
| `products-categories/footer-1024.png` | 1024 | 600 |
| `products-categories/footer-1280.png` | 1280 | 600 |
| `products-categories/header-1024.png` | 1024 | 70 |
| `products-categories/header-1280.png` | 1280 | 132 |
| `shop/footer-1024.png` | 1024 | 600 |
| `shop/footer-1280.png` | 1280 | 600 |
| `shop/header-1024.png` | 1024 | 70 |
| `shop/header-1280.png` | 1280 | 132 |

### حالت‌های ثبت‌شده

| حالت | مسیر | وضعیت |
|---|---|---|
| mega-menu-open | `qa/baseline/states/mega-menu-open/1440.png` | ثبت‌شده |
| mobile-drawer-open | `qa/baseline/states/mobile-drawer-open/390.png` | ثبت‌شده |
| search-open | `qa/baseline/states/search-open/1440.png` | ثبت‌شده |
| mini-cart-open | `qa/baseline/states/mini-cart-open/1440.png` | ثبت‌شده |
| sticky-header | `qa/baseline/states/sticky-header/1440.png` | ثبت‌شده |
| product-card-hover | `qa/baseline/states/product-card-hover/1440.png` | ثبت‌شده |
| quick-view-modal | `qa/baseline/states/quick-view-modal/1440.png` | ثبت‌شده |
| button-hover | `qa/baseline/states/button-hover/1440.png` | ثبت‌شده |
| button-focus | `qa/baseline/states/button-focus/1440.png` | ثبت‌شده |
| input-focus | `qa/baseline/states/input-focus/1440.png` | ثبت‌شده |
| product-swatch | `qa/baseline/states/product-swatch/1440.png` | ثبت‌شده |
| product-tabs | `qa/baseline/states/product-tabs/1440.png` | ثبت‌شده |
| checkout-validation | `qa/baseline/states/checkout-validation/1440.png` | ثبت‌شده |

### اسپک‌ها / توکن‌ها
- فایل اسپک: ۷۰ (+ `_tokens.json`)
- گروه‌های انتخابگر per-page: تا ۳۱ (نمونه home-1440: ۲۱ غیرnull)
- `_tokens.json`: fontFamilies=3، colors≈۲۳، borderRadii=7

### حساب خصوصی (commit نمی‌شود)
`git check-ignore qa/baseline-private` → ignore.

فایل‌های محلی:
- `qa/baseline-private/dashboard/1440.png`
- `qa/baseline-private/orders/1440.png`
- `qa/baseline-private/addresses/1440.png`
- `qa/baseline-private/edit-account/1440.png`
- `qa/baseline-private/downloads/1440.png`
- `qa/baseline-private/wishlist/1440.png`

## تست‌نشده / باقی‌مانده
- thankyou: فاز ۷ با سفارش تست
- sticky ATC اگر در DOM نباشد
- قوانین/همکاری: لینک `#` — صفحه جدا نیست
- `/blog/`: آرشیو بدون نوشتهٔ قابل اتکا برای baseline
- product-sale / product-outofstock جدا: اگر در کاتالوگ shop پیدا نشود ساخته نمی‌شود
- برش‌های state عمداً المانی‌اند (عرض ≠ viewport)

## پرسش‌ها
- آیا لینک‌های قوانین/همکاری باید به صفحات واقعی وصل شوند یا عمداً `#`اند؟
