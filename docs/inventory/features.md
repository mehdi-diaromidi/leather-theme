# فهرست قابلیت‌ها و وابستگی‌های Woodmart (فاز ۱)

فقط آنچه **روی سایت زنده دیده/تایید** شد. موارد مشکوک با «نیاز به تایید export» علامت خورده‌اند.

## قابلیت‌های فرانت دیده‌شده

| قابلیت | شواهد | مهاجرت داده / جایگزین |
|---|---|---|
| Header Builder | `header.whb-header whb-header_371598 whb-scroll-stick whb-sticky-real` | بازسازی با `get_theme_mod` + قالب PHP؛ داده از export (`wd_header_builder` / layouts) |
| Top bar | متن ساعت کاری + تلفن | theme_mod |
| Sticky header | کلاس‌های `whb-scroll-stick whb-sticky-real` | JS/CSS قالب |
| مگامنو / دراپ‌داون | لینک‌های تو در توی زنانه/مردانه؛ `wd-dropdown` | منو WP + meta آیتم منو → ساختار خودمان |
| جستجوی ایجکس هدر | `.wd-header-search` / ajax-search | پیاده‌سازی سبک در قالب در صورت تایید inventory |
| Mini-cart | `.wd-header-cart` | ووکامرس + UI قالب |
| Wishlist | آیکن هدر + صفحه `/علاقه-مندی-ها/` | اگر در فروش لازم است داخل قالب/پلاگین |
| Compare | صفحه `/مقایسه/` در sitemap؛ آیکن هدر در home با selector رایج false بود | تایید استفاده واقعی در فاز بعد |
| Mobile nav / drawer | `.mobile-nav` / `wd-header-mobile-nav` | قالب |
| AJAX shop | `body.woodmart-ajax-shop-on` | اختیاری؛ در صورت نیاز با JS خودمان |
| Product hover buttons | `wd-hover-buttons-on-hover` | CSS/JS قالب |
| Quick view | روی کارت‌های `.wd-product` | اگر نگه داشته شود، پیاده‌سازی مستقل |
| Attribute swatches | روی کارت محصول `hasSwatch` | **مهاجرت term-meta** از export |
| Instagram section | ویجت `elementor-widget-bagllet-instagram` | پلاگین همراه (نه Woodmart) |
| Hero / category / story | ویجت‌های bagllet-* در المنتور | پلاگین همراه |
| Footer Woodmart | `footer.wd-footer.footer-container` | منطق فعلی در bagllet-widgets/footer نیز هست → انتقال به قالب |
| Categories accordion | `categories-accordion-on` | رفتار آرشیو |

## CPT / متای Woodmart (از sitemap و ساختار)

| مورد | شواهد | مهاجرت |
|---|---|---|
| `cms_block` | ۵ پست در export | HTML Blocks → بلوک/الگوی خودمان یا نگه‌داری موقت |
| `woodmart_layout` | ۶ پست در export | Header/layouts → قالب |
| `elementor_library` | ۲ قالب | وابسته به صفحه اصلی/المنتور |
| term-meta swatch | ۱۰ taxonomy ویژگی (`pa_color`، `pa_brand`، …) | مهاجرت term-meta از export |
| menu item meta | منوی `Main navigation` (۴) + `دسته‌بندی محصولات` (۳۷) | مگامنو از meta آیتم‌ها |

## ویجت‌ها / شورت‌کدهای Woodmart داخل المنتور

صفحه‌ی اصلی عمدتاً ویجت‌های **bagllet-*** دارد (نه wd Elementor widgets). هر ویجت/شورت‌کد `wd_` باقی‌مانده باید از دیتای المنتور در export/صفحات دیگر استخراج شود — خلاصه‌ی خام در `qa/data/` پس از export.

## موارد ندیده‌شده در لحظه‌ی مشاهده

- Cookie bar
- Popup پیش‌فرض
- اسلایدر Woodmart کلاسیک روی home (hero سفارشی bagllet است)

## نقشه‌ی مهاجرت خلاصه

1. هدر/فوتر/منو → قالب `bagllet` + theme_mod  
2. فروشگاه/محصول/سبد → override ووکامرس قالب  
3. swatch/wishlist/compare/quick view → فقط اگر در baseline و کسب‌وکار لازم‌اند  
4. صفحه اصلی → نگه داشتن Elementor + bagllet-widgets  
5. حذف وابستگی به `wd-` / `xts-` / `woodmart` در خروجی نهایی  
