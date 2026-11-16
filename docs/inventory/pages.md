# فهرست صفحات و حالت‌ها (فاز ۱)

منبع: sitemap + مشاهده زنده + ووکامرس. مسیرهای عمومی در `qa/baseline/`؛ حساب لاگین‌شده فقط در `qa/baseline-private/` (gitignore).

## صفحات محتوایی / فروشگاهی

| id | مسیر | نوع | baseline عمومی |
|---|---|---|---|
| home | `/` | Elementor صفحه اصلی | بله |
| about | `/about/` | درباره | بله |
| contact | `/contact-us/` | تماس | بله |
| shop | `/shop/` | فروشگاه | بله |
| cat-womens / cat-mens / زیردسته‌ها | `/product-category/...` | دسته | بله |
| products-categories | `/products-categories/` | صفحه دسته‌ها | بله |
| product-sample (+ variable/sale/outofstock اگر در کاتالوگ باشد) | `/product/...` | محصول | بله |
| cart-empty | `/cart/` | سبد خالی (مهمان) | بله |
| cart-full | `/cart/` پس از add-to-cart مهمان | سبد پر | بله (R2) |
| checkout | `/checkout/` مهمان با سبد | فرم تسویه خالی (بدون PII) | بله (R2)؛ نسخهٔ قبلی به‌خاطر سبد خالی redirect بود |
| thankyou | صفحه تشکر | — | **با سفارش تست در فاز ۷** |
| wishlist / compare | صفحات Woodmart | بله |
| search-results / search-empty / 404 | جستجو / ۴۰۴ | بله |
| legal / affiliate | لینک فوتر «قوانین و مقررات» و «همکاری در فروش» | فعلاً `href="#"` (صفحه جدا نیست) — در inventory ثبت شد |
| blog | `/blog/` | آرشیو بدون نوشتهٔ قابل اتکا | خارج از baseline (صریح) |
| home-tmp | `/home-tmp/` | غیرتولیدی | خارج از baseline |

## حساب کاربری

| id | مسیر | baseline |
|---|---|---|
| my-account (مهمان) | `/my-account/` | عمومی = فرم ورود |
| dashboard / orders / addresses / edit-account / downloads / wishlist | زیر `/my-account/…` | فقط `qa/baseline-private/` |

پیشخوان لاگین‌شده (woodmart-plus/Elementor): سفارش‌ها، دانلودها، تجربه‌های خرید، علاقه‌مندی، کیف پول، تیکت، اطلاعیه، کدتخفیف، نشانی، جزئیات، صورتحساب. کیف پول/تیکت/اطلاعیه/ورود پیامکی خارج از قالب فروش (یکپارچه‌سازی اختیاری).

## حالت‌های تعاملی (۱.ب)

| حالت | مسیر فایل | وضعیت |
|---|---|---|
| مگامنو باز (1440) | `qa/baseline/states/mega-menu-open/1440.png` | ثبت‌شده |
| drawer موبایل (390) | `qa/baseline/states/mobile-drawer-open/390.png` | ثبت‌شده |
| جستجوی باز | `qa/baseline/states/search-open/1440.png` | ثبت‌شده |
| mini-cart باز | `qa/baseline/states/mini-cart-open/1440.png` | ثبت‌شده |
| sticky header | `qa/baseline/states/sticky-header/1440.png` | ثبت‌شده |
| hover کارت محصول | `qa/baseline/states/product-card-hover/1440.png` | ثبت‌شده |
| quick view | `qa/baseline/states/quick-view-modal/1440.png` | ثبت‌شده |
| hover دکمه | `qa/baseline/states/button-hover/1440.png` | ثبت‌شده |
| focus دکمه | `qa/baseline/states/button-focus/1440.png` | ثبت‌شده |
| focus فیلد فرم | `qa/baseline/states/input-focus/1440.png` | ثبت‌شده |
| swatch محصول | `qa/baseline/states/product-swatch/1440.png` | ثبت‌شده |
| تب محصول | `qa/baseline/states/product-tabs/1440.png` | ثبت‌شده |
| خطای اعتبارسنجی تسویه | `qa/baseline/states/checkout-validation/1440.png` | ثبت‌شده |
| sticky ATC | — | در صورت وجود در DOM فاز ۶ تکمیل می‌شود |
| cookie/popup پیش‌فرض | — | در مشاهده دیده نشد |

ستون «وضعیت» فقط وقتی «ثبت‌شده» است که فایل روی دیسک باشد (اسکریپت `npm run qa:states`).

## عرض‌ها

- تمام‌صفحه: 1440 / 768 / 390 (عرض فایل = عرض هدف)
- برش header + footer: 1280 / 1024 — فوتر واقعی `footer.bgf`
- خروجی: `qa/baseline/<id>/<width>.png` و `qa/baseline/specs/`
- سرریز: `docs/inventory/overflow.md`
