/** Shared helpers for baseline / states capture. */
import fs from 'fs';
import path from 'path';

export const STYLE_PROPS = [
	'fontFamily', 'fontSize', 'fontWeight', 'lineHeight', 'letterSpacing', 'color',
	'backgroundColor', 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
	'marginTop', 'marginRight', 'marginBottom', 'marginLeft', 'borderTopWidth',
	'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth', 'borderTopColor',
	'borderTopStyle', 'borderRadius', 'boxShadow', 'transition', 'transitionDuration',
	'transitionTimingFunction', 'zIndex', 'display', 'width', 'height', 'maxWidth',
	'gap', 'textAlign', 'textDecoration', 'opacity', 'transform',
];

export const KEY_SELECTORS = {
	header: 'header.whb-header, header',
	topBar: '.whb-top-bar, .whb-row-top, .topbar-contents',
	logo: '.wd-logo img, .site-logo img, .whb-header .wd-header-logo img, .wd-logo',
	mainNav: '.wd-nav.wd-nav-main, .main-nav, nav.wd-header-main-nav',
	search: '.wd-header-search, .searchform',
	cart: '.wd-header-cart, .woodmart-shopping-cart',
	wishlist: '.wd-header-wishlist',
	footer: 'footer.bgf, footer.wd-footer, footer',
	footerColumn: 'footer.bgf .container, footer .footer-column, footer .wpb_wrapper, footer .wd-widget',
	productCard: '.wd-product, .product-grid-item',
	addToCartBtn: '.single_add_to_cart_button, .button.product_type_simple, .add_to_cart_button',
	buttonSecondary: '.btn.btn-bordered, .button.alt, a.button.alt',
	price: '.price, .amount',
	heading: 'h1, .entry-title, .wd-page-title',
	h2: 'h2',
	h3: 'h3',
	bodyText: 'body .entry-content p, body .woocommerce-product-details__short-description, body p',
	breadcrumb: '.woocommerce-breadcrumb, .wd-breadcrumbs, nav.woocommerce-breadcrumb',
	pagination: '.woocommerce-pagination, .wd-pagination, ul.page-numbers',
	saleBadge: '.onsale, .wd-label-sale, .wd-product-labels',
	inputText: 'input.input-text, input[type="text"], input[type="email"], input[type="tel"]',
	select: 'select',
	checkbox: 'input[type="checkbox"]',
	qty: 'input.qty, .quantity input',
	tabs: '.woocommerce-tabs, .wd-tabs, .wc-tabs',
	notice: '.woocommerce-message, .woocommerce-info, .woocommerce-error, .wd-notice',
	gallery: '.woocommerce-product-gallery, .wd-gallery-images',
	swatch: '.wd-swatches-product, .wd-swatches, .variations_form',
	stickyHeader: 'header.whb-sticked, header.whb-header',
	mobileNav: '.mobile-nav, .wd-side-hidden, .wd-nav-mobile',
	container: '.container, .wd-page-content > .container, .main-page-wrapper .container',
};

export async function preparePage(page, { maskSelectors = [], light = false } = {}) {
	await page.emulateMedia({ reducedMotion: 'reduce' });
	await page.addStyleTag({
		content: `
      #wpadminbar,.wd-skip-links{display:none!important}
      html,body{margin-top:0!important;max-width:100%!important;overflow-x:clip!important;scrollbar-width:none!important}
      body{width:100%!important}
      html::-webkit-scrollbar,body::-webkit-scrollbar{width:0!important;height:0!important}
      *,*::before,*::after{animation:none!important;transition:none!important;scroll-behavior:auto!important}
    `,
	});

	const scrollDelay = light ? 40 : 100;
	await page.evaluate(async ({ scrollDelay, light }) => {
		const delay = (ms) => new Promise((r) => setTimeout(r, ms));
		const step = Math.max(light ? 500 : 300, Math.floor(window.innerHeight * (light ? 1 : 0.7)));
		const maxY = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
		for (let y = 0; y <= maxY; y += step) {
			window.scrollTo(0, y);
			await delay(scrollDelay);
		}
		window.scrollTo(0, maxY);
		await delay(light ? 150 : 400);
		document.querySelectorAll('img').forEach((img) => {
			const ds = img.getAttribute('data-src') || img.getAttribute('data-lazy-src') || img.getAttribute('data-original');
			if (ds && (!img.src || img.src.includes('data:image') || img.classList.contains('lazyload'))) {
				img.src = ds;
			}
			img.loading = 'eager';
			if (img.decode) img.decode().catch(() => {});
		});
		document.querySelectorAll('source[data-srcset]').forEach((s) => {
			s.setAttribute('srcset', s.getAttribute('data-srcset'));
		});
		document.querySelectorAll('.swiper').forEach((el) => {
			if (el.swiper) {
				try {
					el.swiper.autoplay?.stop?.();
					el.swiper.slideTo(0, 0);
				} catch (_) {}
			}
		});
		await delay(light ? 200 : 500);
		window.scrollTo(0, 0);
		await delay(120);
	}, { scrollDelay, light });

	try {
		await page.waitForLoadState('networkidle', { timeout: light ? 8000 : 20000 });
	} catch (_) {}

	const imgStats = await page.evaluate(async () => {
		const delay = (ms) => new Promise((r) => setTimeout(r, ms));
		const visibleImgs = () =>
			[...document.images].filter((i) => {
				if (!i.src || i.src.startsWith('data:')) return false;
				const st = getComputedStyle(i);
				if (st.visibility === 'hidden' || st.display === 'none' || st.opacity === '0') return false;
				const r = i.getBoundingClientRect();
				if (r.width < 2 || r.height < 2) return false;
				return true;
			});
		const deadline = Date.now() + 20000;
		while (Date.now() < deadline) {
			const imgs = visibleImgs();
			const pending = imgs.filter((i) => !i.complete || i.naturalWidth === 0);
			if (pending.length === 0) return { total: imgs.length, incomplete: 0 };
			await delay(250);
		}
		const imgs = visibleImgs();
		const pending = imgs.filter((i) => !i.complete || i.naturalWidth === 0);
		return {
			total: imgs.length,
			incomplete: pending.length,
			samples: pending.slice(0, 5).map((i) => i.src.slice(0, 120)),
		};
	});

	for (const sel of maskSelectors) {
		await page.evaluate((s) => {
			document.querySelectorAll(s).forEach((el) => {
				el.style.visibility = 'hidden';
			});
		}, sel).catch(() => {});
	}

	const overflow = await page.evaluate(() => ({
		clientWidth: document.documentElement.clientWidth,
		scrollWidth: document.documentElement.scrollWidth,
		innerWidth: window.innerWidth,
	}));

	return { imgStats, overflow };
}

export async function collectSpecs(page, pageId, width, selectors = KEY_SELECTORS) {
	return page.evaluate(
		({ selectors, props, pageId, width }) => {
			const out = { pageId, width, capturedAt: new Date().toISOString(), elements: {} };
			for (const [name, sel] of Object.entries(selectors)) {
				const el = document.querySelector(sel);
				if (!el) {
					out.elements[name] = null;
					continue;
				}
				const cs = getComputedStyle(el);
				const rect = el.getBoundingClientRect();
				const style = {};
				for (const p of props) style[p] = cs[p];
				out.elements[name] = {
					selector: sel,
					bbox: {
						x: Math.round(rect.x),
						y: Math.round(rect.y),
						width: Math.round(rect.width),
						height: Math.round(rect.height),
					},
					style,
				};
			}
			return out;
		},
		{ selectors, props: STYLE_PROPS, pageId, width }
	);
}

export async function shotFull(page, filePath, width) {
	fs.mkdirSync(path.dirname(filePath), { recursive: true });
	const fullHeight = await page.evaluate(() =>
		Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight)
	);
	// fullPage می‌تواند به‌خاطر سرریز افقی عریض‌تر از viewport شود؛
	// با viewport به ارتفاع کامل، عرض دقیقاً برابر width می‌ماند.
	const prev = page.viewportSize() || { width, height: 900 };
	const capped = Math.min(Math.max(fullHeight, prev.height || 900), 16000);
	await page.setViewportSize({ width, height: capped });
	await page.waitForTimeout(80);
	await page.screenshot({
		path: filePath,
		fullPage: false,
		animations: 'disabled',
	});
	await page.setViewportSize(prev);
	return { width, expectedWidth: width, height: fullHeight, capturedHeight: capped };
}

export async function shotLocator(page, locator, filePath) {
	fs.mkdirSync(path.dirname(filePath), { recursive: true });
	const count = await locator.count();
	if (!count) return false;
	try {
		await locator.first().scrollIntoViewIfNeeded({ timeout: 5000 });
		await locator.first().screenshot({ path: filePath, animations: 'disabled', timeout: 15000 });
	} catch (_) {
		return false;
	}
	return fs.existsSync(filePath);
}

export function buildTokens(specFiles) {
	const fonts = new Set();
	const colors = new Set();
	const radii = new Set();
	const shadows = new Set();
	const transitions = new Set();
	const sizes = {};
	for (const file of specFiles) {
		const data = JSON.parse(fs.readFileSync(file, 'utf8'));
		for (const [name, el] of Object.entries(data.elements || {})) {
			if (!el?.style) continue;
			if (el.style.fontFamily) fonts.add(el.style.fontFamily);
			if (el.style.color) colors.add(el.style.color);
			if (el.style.backgroundColor) colors.add(el.style.backgroundColor);
			if (el.style.borderRadius) radii.add(el.style.borderRadius);
			if (el.style.boxShadow && el.style.boxShadow !== 'none') shadows.add(el.style.boxShadow);
			if (el.style.transition && el.style.transition !== 'all 0s ease 0s') transitions.add(el.style.transition);
			if (el.style.fontSize) {
				sizes[name] = sizes[name] || {};
				sizes[name][data.width] = el.style.fontSize;
			}
		}
	}
	return {
		generatedAt: new Date().toISOString(),
		fontFamilies: [...fonts],
		colors: [...colors].slice(0, 80),
		borderRadii: [...radii],
		boxShadows: [...shadows].slice(0, 40),
		transitions: [...transitions].slice(0, 40),
		fontSizeByElement: sizes,
	};
}
