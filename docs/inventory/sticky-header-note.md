# هدر sticky (F10)

پس از اسکرول تدریجی تا ~۹۰۰px و `mouse.wheel`، هیچ المان `position:fixed/sticky` مربوط به هدر در بالای viewport ظاهر نشد. ردیف‌های هدر کلاس `whb-not-sticky-row` دارند (با وجود `whb-scroll-stick whb-sticky-real` روی `<header>`).

برش `qa/baseline/states/sticky-header/1440.png` = بالای viewport بعد از اسکرول (~۱۵۰px) به‌عنوان مدرک «اسکرول انجام شد؛ هدر نچسبید».

اگر در فاز ۳ sticky لازم است، رفتار باید در قالب جدید پیاده شود (وضعیت فعلی سایت زنده: غیرفعال/بی‌اثر).
