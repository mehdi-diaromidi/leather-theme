(function () {
	'use strict';

	var footer = document.querySelector('.bgf');
	if (!footer) {
		return;
	}

	var mobile = window.matchMedia('(max-width: 0px)') /* آکاردئون موبایل غیرفعال است */;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	var accordions = footer.querySelectorAll('[data-bgf-acc]');

	function setPanelState(acc, open) {
		var btn = acc.querySelector('.bgf__acc-btn');
		var panel = acc.querySelector('.bgf__acc-panel');
		acc.classList.toggle('is-open', open);
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.inert = !open;
	}

	// On tablet/desktop the accordion buttons are plain column headings.
	function syncMode() {
		accordions.forEach(function (acc) {
			var btn = acc.querySelector('.bgf__acc-btn');
			var panel = acc.querySelector('.bgf__acc-panel');
			if (mobile.matches) {
				btn.removeAttribute('tabindex');
				btn.removeAttribute('aria-disabled');
				setPanelState(acc, acc.classList.contains('is-open'));
			} else {
				btn.setAttribute('tabindex', '-1');
				btn.setAttribute('aria-disabled', 'true');
				btn.removeAttribute('aria-expanded');
				panel.inert = false;
			}
		});
	}

	accordions.forEach(function (acc) {
		acc.querySelector('.bgf__acc-btn').addEventListener('click', function () {
			if (!mobile.matches) {
				return;
			}
			setPanelState(acc, !acc.classList.contains('is-open'));
		});
	});

	if (mobile.addEventListener) {
		mobile.addEventListener('change', syncMode);
	}
	syncMode();

	var top = footer.querySelector('.bgf__top');
	if (top) {
		top.addEventListener('click', function (event) {
			event.preventDefault();
			window.scrollTo({ top: 0, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
		});
	}
})();
