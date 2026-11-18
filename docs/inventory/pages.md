# فهرست صفحات و حالت‌ها (فاز ۱)

منبع: sitemap + مشاهده زنده + ووکامرس. مسیرهای عمومی در `qa/baseline/`؛ حساب لاگین‌شده فقط در `qa/baseline-private/` (gitignore).

## صفحات محتوایی / فروشگاهی

| id | مسیر | نوع | baseline عمومی |
|---|---|---|---|
| home | `/` | Elementor صفحه اصلی | بله — دو کاشی دسته بدون تصویر: `f13-missing-category-images.md` |
| about | `/about/` | درباره | بله |
| contact | `/contact-us/` | تماس | بله |
| shop | `/shop/` | فروشگاه | بله |
| cat-womens / cat-mens / زیردسته‌ها | `/product-category/...` | دسته | بله |
| products-categories | `/products-categories/` | صفحه دسته‌ها | بله |
| product-sample | `/product/...` | محصول | بله |
| cart-empty | `/cart/` | سبد خالی (مهمان) | بله |
| cart-full | `/cart/` پس از add-to-cart مهمان | سبد پر | بله |
| checkout | `/checkout/` مهمان با سبد | فرم تسویه خالی (بدون PII) | بله |
| thankyou | صفحه تشکر | — | **با سفارش تست در فاز ۷** |
| wishlist / compare | صفحات Woodmart | بله |
| search-results / search-empty / 404 | جستجو / ۴۰۴ | بله |
| legal / affiliate | لینک فوتر | `href="#"` — parity؛ در قالب از Customizer قابل تنظیم |
| blog | `/blog/` | آرشیو بدون نوشته | خارج از baseline |
| home-tmp | `/home-tmp/` | غیرتولیدی | خارج از baseline |

## حساب کاربری

| id | مسیر | baseline |
|---|---|---|
| my-account (مهمان) | `/my-account/` | عمومی = فرم ورود |
| dashboard / orders / addresses / edit-account / downloads / wishlist | زیر `/my-account/…` | فقط `qa/baseline-private/` |

## حالت‌های تعاملی (۱.ب)

| حالت | مسیر فایل | وضعیت |
|---|---|---|
| مگامنو باز (1440) | `qa/baseline/states/mega-menu-open/1440.png` | ثبت‌شده (R3) |
| drawer موبایل (390) | `qa/baseline/states/mobile-drawer-open/390.png` | ثبت‌شده |
| جستجوی باز + نتایج | `qa/baseline/states/search-open/1440.png` | ثبت‌شده (R3) |
| mini-cart باز | `qa/baseline/states/mini-cart-open/1440.png` | ثبت‌شده (R3) |
| sticky header | `qa/baseline/states/sticky-header/1440.png` | اسکرول ثبت شد؛ sticky روی زنده غیرفعال — `sticky-header-note.md` |
| hover کارت محصول | `qa/baseline/states/product-card-hover/1440.png` | ثبت‌شده |
| quick view | `qa/baseline/states/quick-view-modal/1440.png` | ثبت‌شده |
| hover ATC | `qa/baseline/states/button-hover/1440.png` (+ `button-hover-atc`) | ثبت‌شده (R3) |
| focus ATC | `qa/baseline/states/button-focus/1440.png` (+ `button-focus-atc`) | ثبت‌شده (R3) |
| hover دسته / تسویه / لینک منو / فوتر | `button-hover-category`, `button-hover-checkout`, `link-hover-nav`, `link-hover-footer` | ثبت‌شده (R3) |
| focus فیلد فرم | `qa/baseline/states/input-focus/1440.png` | ثبت‌شده |
| swatch / تب محصول | `product-swatch`, `product-tabs` | ثبت‌شده |
| خطای اعتبارسنجی تسویه | `qa/baseline/states/checkout-validation/1440.png` | ثبت‌شده |
| sticky ATC | — | فاز ۶ در صورت وجود DOM |

## عرض‌ها

- تمام‌صفحه: 1440 / 768 / 390
- برش header + footer: 1280 / 1024 — فوتر واقعی `footer.bgf`
- سرریز: `docs/inventory/overflow.md`
