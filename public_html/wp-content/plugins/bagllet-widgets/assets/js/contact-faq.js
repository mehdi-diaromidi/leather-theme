/* Bagllet FAQ: انیمیشن نرم باز/بسته <details> در صفحه‌ی تماس با ما. */
(function () {
	'use strict';

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	var MS = 280;

	function setH(panel, px) {
		panel.style.setProperty('height', px + 'px', 'important');
		panel.style.setProperty('max-height', px + 'px', 'important');
	}

	function clearAnim(panel) {
		panel.style.removeProperty('height');
		panel.style.removeProperty('max-height');
		panel.style.removeProperty('opacity');
		panel.style.removeProperty('overflow');
		panel.style.removeProperty('transition');
		panel.style.removeProperty('padding-bottom');
	}

	function run(panel, from, to, done) {
		panel.style.overflow = 'hidden';
		setH(panel, from);
		panel.style.opacity = from === 0 ? '0' : '1';
		panel.getBoundingClientRect();
		panel.style.transition =
			'height ' + MS + 'ms cubic-bezier(.2,.7,.2,1), max-height ' + MS + 'ms cubic-bezier(.2,.7,.2,1), opacity ' + MS + 'ms ease';
		requestAnimationFrame(function () {
			setH(panel, to);
			panel.style.opacity = to === 0 ? '0' : '1';
		});

		var finished = false;
		function end() {
			if (finished) {
				return;
			}
			finished = true;
			panel.removeEventListener('transitionend', onEnd);
			window.clearTimeout(fallback);
			done();
		}
		function onEnd(e) {
			if (e.target === panel && (e.propertyName === 'height' || e.propertyName === 'max-height')) {
				end();
			}
		}
		panel.addEventListener('transitionend', onEnd);
		var fallback = window.setTimeout(end, MS + 100);
	}

	function init(item) {
		var summary = item.querySelector('summary');
		var panel = item.querySelector('.bgl-fq__a');
		if (!summary || !panel) {
			return;
		}

		var busy = false;

		summary.addEventListener('click', function (e) {
			e.preventDefault();
			if (busy) {
				return;
			}
			busy = true;

			if (item.open) {
				var from = panel.scrollHeight;
				run(panel, from, 0, function () {
					item.open = false;
					clearAnim(panel);
					busy = false;
				});
			} else {
				panel.style.overflow = 'hidden';
				setH(panel, 0);
				panel.style.opacity = '0';
				item.open = true;
				var to = panel.scrollHeight || panel.getBoundingClientRect().height;
				if (!to) {
					clearAnim(panel);
					var prev = panel.style.position;
					panel.style.position = 'absolute';
					panel.style.visibility = 'hidden';
					panel.style.height = 'auto';
					to = panel.scrollHeight;
					panel.style.position = prev;
					panel.style.visibility = '';
					setH(panel, 0);
				}
				run(panel, 0, to, function () {
					clearAnim(panel);
					busy = false;
				});
			}
		});
	}

	function boot() {
		[].forEach.call(document.querySelectorAll('.bgl-fq__it'), init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
