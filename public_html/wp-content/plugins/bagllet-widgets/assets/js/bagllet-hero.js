/* Bagllet hero slider: بدون کتابخانه. زمان‌بندی با انیمیشن CSS نوار پیشرفت انجام می‌شود. */
(function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function init(root) {
		if (!root || root.getAttribute('data-bgl-init')) {
			return;
		}
		root.setAttribute('data-bgl-init', '1');

		var slides = [].slice.call(root.querySelectorAll('.bgl-hero__slide'));
		var n = slides.length;
		if (!n) {
			return;
		}

		var bars = [].slice.call(root.querySelectorAll('.bgl-hero__bar'));
		var prev = root.querySelector('.bgl-hero__nav--prev');
		var next = root.querySelector('.bgl-hero__nav--next');
		var track = root.querySelector('.bgl-hero__track');
		var editing = document.body.classList.contains('elementor-editor-active');
		var auto = root.getAttribute('data-autoplay') === 'yes' && !reduce && !editing && n > 1;
		var rtl = window.getComputedStyle(root).direction === 'rtl';
		var interval = Math.max(3, parseFloat(root.getAttribute('data-interval')) || 6) * 1000;
		var idx = 0;
		var hover = false;
		var focus = false;
		var touch = false;
		var hidden = document.hidden;
		var offscreen = false;

		root.style.setProperty('--bgl-dur', interval + 'ms');
		if (auto) {
			root.classList.add('is-auto');
		}

		function updatePause() {
			root.classList.toggle('is-paused', hover || focus || touch || hidden || offscreen);
		}

		var show = function (i) {
			idx = (i + n) % n;

			slides.forEach(function (s, k) {
				var on = k === idx;
				s.classList.toggle('is-active', on);
				if (on) {
					s.removeAttribute('aria-hidden');
					s.removeAttribute('inert');
				} else {
					s.setAttribute('aria-hidden', 'true');
					s.setAttribute('inert', '');
				}
			});

			bars.forEach(function (b) {
				b.classList.remove('is-active');
				b.removeAttribute('aria-current');
			});
			void root.offsetWidth; /* ریست انیمیشن نوار پیشرفت */
			if (bars[idx]) {
				bars[idx].classList.add('is-active');
				bars[idx].setAttribute('aria-current', 'true');
			}
		};

		/* وقتی نوار پیشرفت تمام شد، اسلاید بعدی */
		bars.forEach(function (b, k) {
			b.addEventListener('click', function () {
				show(k);
			});
			var fill = b.querySelector('.bgl-hero__bar-in');
			if (fill) {
				fill.addEventListener('animationend', function () {
					if (auto && k === idx) {
						show(idx + 1);
					}
				});
			}
		});

		if (prev) {
			prev.addEventListener('click', function () {
				show(idx - 1);
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				show(idx + 1);
			});
		}

		/* مکث: ماوس، فوکوس، لمس، تب پنهان، خارج از دید */
		root.addEventListener('mouseenter', function () {
			hover = true;
			updatePause();
		});
		root.addEventListener('mouseleave', function () {
			hover = false;
			updatePause();
		});
		root.addEventListener('focusin', function () {
			focus = true;
			updatePause();
		});
		root.addEventListener('focusout', function () {
			focus = false;
			updatePause();
		});
		document.addEventListener('visibilitychange', function () {
			hidden = document.hidden;
			updatePause();
		});
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				offscreen = !entries[0].isIntersecting;
				updatePause();
			}, { threshold: 0.2 }).observe(root);
		}

		/* سوایپ لمسی (در RTL جهت برعکس است) */
		var sx = 0;
		var sy = 0;
		var tracking = false;
		track.addEventListener('pointerdown', function (e) {
			if (e.pointerType === 'mouse') {
				return;
			}
			tracking = true;
			touch = true;
			sx = e.clientX;
			sy = e.clientY;
			updatePause();
		});
		function endTouch(e, cancel) {
			if (!tracking) {
				return;
			}
			tracking = false;
			touch = false;
			updatePause();
			if (cancel) {
				return;
			}
			var dx = e.clientX - sx;
			var dy = e.clientY - sy;
			if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy) * 1.4) {
				var forward = rtl ? dx > 0 : dx < 0;
				show(forward ? idx + 1 : idx - 1);
			}
		}
		track.addEventListener('pointerup', function (e) {
			endTouch(e, false);
		});
		track.addEventListener('pointercancel', function (e) {
			endTouch(e, true);
		});

		/* کیبورد */
		root.addEventListener('keydown', function (e) {
			if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
				return;
			}
			var forward = (e.key === 'ArrowLeft') === rtl;
			show(forward ? idx + 1 : idx - 1);
		});

		show(0);
	}

	function initAll(scope) {
		[].slice.call((scope || document).querySelectorAll('.bgl-hero')).forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initAll();
		});
	} else {
		initAll();
	}

	/* ویرایشگر المنتور */
	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/bagllet-hero.default', function ($el) {
				initAll($el && $el[0] ? $el[0] : document);
			});
		}
	});
})();
