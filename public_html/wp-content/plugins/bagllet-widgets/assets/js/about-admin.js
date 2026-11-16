/* انتخاب تصویر برای متاباکس صفحه‌ی «درباره ما» */
(function ($) {
	'use strict';

	$(document).on('click', '.bga-img .bga-pick', function (e) {
		e.preventDefault();
		var box = $(this).closest('.bga-img');
		var frame = wp.media({
			title: 'انتخاب تصویر',
			button: { text: 'استفاده از این تصویر' },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			var url = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
			box.find('input[type=hidden]').val(a.id);
			box.find('.bga-prev').html('<img src="' + url + '" alt="">');
		});
		frame.open();
	});

	$(document).on('click', '.bga-img .bga-clear', function (e) {
		e.preventDefault();
		var box = $(this).closest('.bga-img');
		box.find('input[type=hidden]').val('0');
		box.find('.bga-prev').html('<span>تصویر پیش‌فرض</span>');
	});
})(jQuery);
