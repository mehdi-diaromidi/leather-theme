# فهرست صفحات و حالت‌ها (فاز ۱)

منبع: `https://bagllet.ir/wp-sitemap.xml` + مشاهده‌ی زنده + ووکامرس.

## صفحات محتوایی / فروشگاهی

| id | مسیر | نوع | یادداشت |
|---|---|---|---|
| home | `/` | صفحه اصلی Elementor (`page-id-77`, template `elementor_header_footer`) | ویجت‌های bagllet: hero، category-grid/card، story، instagram |
| about | `/about/` | درباره ما | قالب/متاباکس پلاگین bagllet-widgets |
| contact | `/contact-us/` | تماس | قالب contact پلاگین |
| shop | `/shop/` | آرشیو فروشگاه | `woodmart-ajax-shop-on` |
| cat-womens | `/product-category/womens-leather/` | دسته والد زنانه | مگامنو زیرمجموعه‌ها |
| cat-mens | `/product-category/mens-leather/` | دسته والد مردانه | |
| cat-womens-bag | `/product-category/womens-leather/womens-leather-bag/` | زیردسته | |
| cat-mens-bag | `/product-category/mens-leather/mens-leather-bag/` | زیردسته | |
| product-* | `/product/...` | تک‌محصول | از فروشگاه resolve می‌شود (نمونه/متغیر/تخفیف/ناموجود در صورت وجود) |
| cart-empty | `/cart/` | سبد خالی | |
| cart-full | — | سبد پر | نیاز به افزودن محصول؛ در baseline تعاملی ثبت می‌شود |
| checkout | `/checkout/` | تسویه | با حساب لاگین‌شده دیده شد؛ سفارش واقعی ثبت نشود |
| my-account | `/my-account/` | حساب | مهمان: فرم ورود پیامکی/ایمیل. لاگین‌شده (مشاهده ۱۴۰۴/۰۸/۲۶): پیشخوان سفارشی woodmart-plus/Elementor با لینک‌های سفارش‌ها، دانلودها، تجربه‌های خرید، علاقه‌مندی، کیف پول، تیکت‌ها، اطلاعیه‌ها، کدهای تخفیف، نشانی، جزئیات حساب، صورتحساب‌ها. اسکرین لاگین‌شده به‌خاطر PII/کدتخفیف در ریپوی عمومی commit نشده |
| my-account-orders | `/my-account/orders/` | سفارش‌ها | نیاز به نشست |
| my-account-addresses | `/my-account/edit-address/` | نشانی | نیاز به نشست |
| my-account-edit | `/my-account/edit-account/` | جزئیات حساب | نیاز به نشست |
| my-account-wallet | (لینک از پیشخوان) | کیف پول | افزونه/قالب پلاس |
| my-account-tickets | (لینک از پیشخوان) | تیکت | افزونه پیامکی/پشتیبانی |
| wishlist | `/علاقه-مندی-ها/` | ویش‌لیست Woodmart | صفحه در sitemap |
| compare | `/مقایسه/` | مقایسه Woodmart | صفحه در sitemap |
| products-categories | `/products-categories/` | صفحه دسته‌ها | در sitemap |
| home-tmp | `/home-tmp/` | صفحه موقت | غیرتولیدی؛ در baseline اصلی نیست |
| search-results | `/?s=کیف&post_type=product` | جستجو با نتیجه | |
| search-empty | `/?s=zzzzznofind999` | جستجو بدون نتیجه | |
| 404 | مسیر ناموجود | ۴۰۴ | |
| thankyou | — | تشکر سفارش | بدون سفارش واقعی در این فاز ضبط نشد |
| legal | — | صفحات قانونی | در sitemap صفحه جدا دیده نشد؛ نیاز به تایید مدیر |

## زیردسته‌های دیده‌شده در مگامنو (علاوه بر sitemap)

زنانه: اکسسوری (دستکش، جاکارتی، کیف پول، کیف لوازم آرایش)، ساک، کمربند، انواع کیف (کوله‌پشتی، دستی، اداری، دوشی، کلاسیکی، کلاچ)، ست زنانه.

مردانه: ست مردانه، اکسسوری (کیف دسته چکی، جاکارتی، …)، کیف، کمربند، …

## حالت‌های تعاملی (۱.ب)

| حالت | محل مشاهده | وضعیت ثبت |
|---|---|---|
| hover/focus/active دکمه و کارت | هدر، کارت محصول `.wd-product` | فهرست‌شده؛ اسکرین baseline حالت پیش‌فرض |
| دراپ‌داون / مگامنو | `header.whb-header`، کلاس‌های `wd-dropdown` | موجود |
| منوی موبایل | `.mobile-nav` / `wd-header-mobile-nav` | موجود |
| mini-cart باز | `.wd-header-cart` | موجود |
| جستجوی باز / ایجکس | `.wd-header-search` | موجود |
| quick view | روی کارت محصول | موجود (`hasQuickView`) |
| sticky header | `whb-scroll-stick whb-sticky-real` | موجود |
| sticky add-to-cart | صفحه محصول | باید در محصول تایید شود |
| swatch ویژگی | کارت محصول | موجود (`hasSwatch`) |
| پاپ‌آپ / cookie bar | صفحه اصلی | در لحظه مشاهده دیده نشد |
| خطاهای فرم / اعلان ووکامرس | تسویه/حساب | تست مخرب انجام نشد |
| loading / AJAX shop | `woodmart-ajax-shop-on` | فعال |

## عرض‌های baseline

- تمام‌صفحه: **1440 / 768 / 390**
- برش header/footer: **1280 / 1024** (+ همان عرض‌های بالا)
- خروجی: `qa/baseline/<page-id>/<width>.png`
- مشخصات: `qa/baseline/specs/<page-id>-<width>.json`
