/**
 * R3: recapture F10–F12 states + F14 token shadows/transitions + F13 category probe.
 * Usage: node qa/baseline/fix-r3-states.mjs
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import crypto from 'crypto';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { preparePage, STYLE_PROPS } from './lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });
const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const OUT = path.join(__dirname, 'states');
const SPECS = path.join(__dirname, 'specs');
const REPORT = path.join(root, 'qa/reports/phase-1');
fs.mkdirSync(OUT, { recursive: true });
fs.mkdirSync(REPORT, { recursive: true });

function md5(file) {
	return crypto.createHash('md5').update(fs.readFileSync(file)).digest('hex');
}

async function lightPrep(page) {
	await page.emulateMedia({ reducedMotion: 'reduce' });
	await page.addStyleTag({
		content: `#wpadminbar,.wd-skip-links{display:none!important} html,body{margin-top:0!important}`,
	});
	await page.waitForTimeout(300);
}

async function saveClip(page, id, width, clip) {
	const dir = path.join(OUT, id);
	fs.mkdirSync(dir, { recursive: true });
	const file = path.join(dir, `${width}.png`);
	await page.screenshot({ path: file, animations: 'disabled', clip });
	return file;
}

async function saveLocator(page, id, width, locator) {
	const dir = path.join(OUT, id);
	fs.mkdirSync(dir, { recursive: true });
	const file = path.join(dir, `${width}.png`);
	await locator.screenshot({ path: file, animations: 'disabled', timeout: 20000 });
	return file;
}

const results = {};
const browser = await chromium.launch({ headless: true });

// ——— F13: category tiles on live home ———
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'fa-IR' });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await preparePage(page); // full lazy-load
	const cats = await page.evaluate(() => {
		const section = [...document.querySelectorAll('h2,h3,.elementor-heading-title')].find((h) =>
			/خرید بر اساس دسته|دسته.?بند/i.test(h.textContent || '')
		);
		const root = section?.closest('.elementor-section, section, .e-con') || document;
		const cards = [...root.querySelectorAll('a, .elementor-widget-image, .wd-cat, .category-grid-item, .product-category')];
		const tiles = [];
		for (const el of cards) {
			const img = el.querySelector('img') || (el.tagName === 'IMG' ? el : null);
			const r = el.getBoundingClientRect();
			if (r.width < 80 || r.height < 80) continue;
			const text = (el.innerText || el.getAttribute('title') || '').trim().slice(0, 60);
			tiles.push({
				text,
				hasImg: !!img,
				src: img?.currentSrc || img?.src || null,
				complete: img ? img.complete : null,
				naturalWidth: img ? img.naturalWidth : null,
				w: Math.round(r.width),
				h: Math.round(r.height),
				top: Math.round(r.top + window.scrollY),
			});
		}
		// Also scan category-looking images in viewport band of category section
		const imgs = [...document.querySelectorAll('img')].filter((img) => {
			const alt = `${img.alt || ''} ${img.closest('a')?.innerText || ''}`;
			return /کیف|دسته|زنانه|مردانه|کوله|چرم/i.test(alt) || /category|cat/i.test(img.className);
		});
		const imgReport = imgs.slice(0, 20).map((img) => ({
			alt: (img.alt || '').slice(0, 40),
			src: (img.currentSrc || img.src || '').slice(0, 120),
			complete: img.complete,
			naturalWidth: img.naturalWidth,
			visible: (() => {
				const r = img.getBoundingClientRect();
				return r.width > 2 && r.height > 2;
			})(),
		}));
		return { tiles: tiles.slice(0, 16), imgReport };
	});
	fs.writeFileSync(path.join(REPORT, 'f13-category-probe.json'), JSON.stringify(cats, null, 2));
	results.f13 = cats;
	console.log('F13 tiles', cats.tiles.length, 'emptyImgs', cats.imgReport.filter((i) => i.naturalWidth === 0).length);
	await ctx.close();
}

// ——— F10 mega / search / sticky ———
{
	const width = 1440;
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR' });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	await page.waitForTimeout(800);

	// Mega menu: real hover on زنانه
	const women = page.locator('header .wd-nav-main a, header .main-nav a, header nav a').filter({ hasText: /^زنانه$/ }).first();
	await women.hover({ force: true });
	await page.waitForTimeout(700);
	// Force-open if theme uses JS hover
	await page.evaluate(() => {
		const link = [...document.querySelectorAll('header a')].find((a) => a.textContent.trim() === 'زنانه');
		const li = link?.closest('li');
		if (!li) return;
		li.classList.add('opened', 'hover', 'focus', 'item-opened');
		li.setAttribute('aria-expanded', 'true');
		const sub = li.querySelector('.wd-dropdown-menu, .wd-dropdown, .sub-menu, .wd-dropdown-megamenu');
		if (sub) {
			sub.style.setProperty('display', 'block', 'important');
			sub.style.setProperty('visibility', 'visible', 'important');
			sub.style.setProperty('opacity', '1', 'important');
			sub.style.setProperty('pointer-events', 'auto', 'important');
			sub.classList.add('wd-opened', 'opened');
		}
	});
	await page.waitForTimeout(400);
	const megaBox = await page.evaluate(() => {
		const link = [...document.querySelectorAll('header a')].find((a) => a.textContent.trim() === 'زنانه');
		const li = link?.closest('li');
		const sub = li?.querySelector('.wd-dropdown-menu, .wd-dropdown, .sub-menu, .wd-dropdown-megamenu');
		const header = document.querySelector('header');
		const hr = header.getBoundingClientRect();
		const sr = sub?.getBoundingClientRect();
		const top = 0;
		const bottom = Math.max(hr.bottom, sr?.bottom || hr.bottom);
		return {
			hasSub: !!sub,
			subW: sr ? Math.round(sr.width) : 0,
			subH: sr ? Math.round(sr.height) : 0,
			clipH: Math.min(Math.ceil(bottom + 20), 900),
			subText: sub ? (sub.innerText || '').slice(0, 80) : '',
		};
	});
	console.log('mega', megaBox);
	results.mega = megaBox;
	const megaFile = await saveClip(page, 'mega-menu-open', width, { x: 0, y: 0, width, height: Math.max(megaBox.clipH, 220) });
	results.megaFile = { file: megaFile, md5: md5(megaFile), bytes: fs.statSync(megaFile).size };

	// Close mega
	await page.mouse.move(10, 10);
	await page.keyboard.press('Escape').catch(() => {});
	await page.waitForTimeout(300);

	// Search: open + type کیف + wait ajax
	await page.evaluate(() => {
		document.querySelector('.wd-header-search a, .wd-tools-element.wd-header-search a, .wd-header-search')?.click();
	});
	await page.waitForTimeout(500);
	const searchInput = page.locator('.wd-search-form input[type=text], .wd-header-search input, form.searchform input[type=text], .wd-search-full input').first();
	if (await searchInput.count()) {
		await searchInput.click({ force: true });
		await searchInput.fill('کیف');
		await page.waitForTimeout(1500);
	} else {
		await page.keyboard.type('کیف', { delay: 80 });
		await page.waitForTimeout(1500);
	}
	const searchInfo = await page.evaluate(() => {
		const panel = document.querySelector(
			'.wd-search-results, .wd-search-full, .wd-dropdown-search, .autocomplete-suggestions, .wd-search-form'
		);
		const r = panel?.getBoundingClientRect();
		const header = document.querySelector('header')?.getBoundingClientRect();
		return {
			hasPanel: !!panel,
			text: panel ? (panel.innerText || '').slice(0, 100) : '',
			h: r ? Math.round(r.height) : 0,
			clipH: Math.min(Math.ceil(Math.max(header?.bottom || 120, r?.bottom || 120) + 10), 900),
		};
	});
	console.log('search', searchInfo);
	results.search = searchInfo;
	const searchFile = await saveClip(page, 'search-open', width, { x: 0, y: 0, width, height: Math.max(searchInfo.clipH, 280) });
	results.searchFile = { file: searchFile, md5: md5(searchFile), bytes: fs.statSync(searchFile).size };

	await page.keyboard.press('Escape').catch(() => {});
	await page.waitForTimeout(300);

	// Sticky: scroll ~600px, crop top viewport
	await page.evaluate(() => window.scrollTo(0, 600));
	await page.waitForTimeout(600);
	const stickyInfo = await page.evaluate(() => {
		const h = document.querySelector('header');
		return {
			classes: h?.className || '',
			sticked: /sticked|sticky|whb-sticked/i.test(h?.className || ''),
			scrollY: window.scrollY,
		};
	});
	console.log('sticky', stickyInfo);
	results.sticky = stickyInfo;
	const stickyFile = await saveClip(page, 'sticky-header', width, { x: 0, y: 0, width, height: 160 });
	results.stickyFile = { file: stickyFile, md5: md5(stickyFile), bytes: fs.statSync(stickyFile).size };

	await ctx.close();
}

// ——— F11 mini-cart ———
{
	const width = 1440;
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR' });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	const href = await page.locator('a[href*="/product/"]').first().getAttribute('href');
	await page.goto(href.startsWith('http') ? href : `${SITE}${href}`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	await page.evaluate(() => {
		const sw = document.querySelector('.wd-swatches-product .wd-swatch:not(.wd-disabled)');
		sw?.click();
		const sel = document.querySelector('.variations select');
		if (sel && sel.options.length > 1) {
			sel.selectedIndex = 1;
			sel.dispatchEvent(new Event('change', { bubbles: true }));
		}
	});
	await page.waitForTimeout(400);
	await page.locator('.single_add_to_cart_button').click({ force: true });
	await page.waitForTimeout(1500);
	// Open mini cart
	await page.evaluate(() => {
		document.querySelector('.wd-header-cart a, a.cart-contents, .wd-tools-element.wd-header-cart a')?.click();
	});
	await page.waitForTimeout(1000);
	const cartInfo = await page.evaluate(() => {
		const sels = [
			'.cart-widget-side',
			'.wd-side-cart',
			'.wd-dropdown-cart',
			'.widget_shopping_cart',
			'#cart-widget-side',
			'.shopping-cart',
		];
		for (const s of sels) {
			const el = document.querySelector(s);
			if (!el) continue;
			const r = el.getBoundingClientRect();
			const style = getComputedStyle(el);
			if (r.width > 50 && r.height > 50 && style.visibility !== 'hidden' && style.display !== 'none') {
				return {
					sel: s,
					w: Math.round(r.width),
					h: Math.round(r.height),
					text: (el.innerText || '').slice(0, 120),
					x: Math.round(r.x),
					y: Math.round(r.y),
				};
			}
		}
		return null;
	});
	console.log('minicart', cartInfo);
	results.minicart = cartInfo;
	let miniFile;
	if (cartInfo) {
		miniFile = await saveLocator(page, 'mini-cart-open', width, page.locator(cartInfo.sel).first());
	} else {
		// fallback: right-side strip
		miniFile = await saveClip(page, 'mini-cart-open', width, { x: width - 420, y: 0, width: 420, height: 900 });
	}
	results.miniFile = { file: miniFile, md5: md5(miniFile), bytes: fs.statSync(miniFile).size };
	await ctx.close();
}

// ——— F12 button hover/focus meaningful crops ———
{
	const width = 1440;
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR' });
	const page = await ctx.newPage();

	// ATC hover on product
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	const href = await page.locator('a[href*="/product/"]').first().getAttribute('href');
	await page.goto(href.startsWith('http') ? href : `${SITE}${href}`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	const atc = page.locator('.single_add_to_cart_button').first();
	await atc.scrollIntoViewIfNeeded();
	await atc.hover({ force: true });
	await page.waitForTimeout(200);
	await saveLocator(page, 'button-hover-atc', width, atc);
	await atc.focus();
	await page.waitForTimeout(150);
	await saveLocator(page, 'button-focus-atc', width, atc);

	// Category CTA on home
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	const catBtn = page.locator('a.btn, a.button, .elementor-button').filter({ hasText: /مشاهده|محصولات|خرید/ }).first();
	if (await catBtn.count()) {
		await catBtn.scrollIntoViewIfNeeded();
		await catBtn.hover({ force: true });
		await page.waitForTimeout(200);
		await saveLocator(page, 'button-hover-category', width, catBtn);
		await catBtn.focus();
		await saveLocator(page, 'button-focus-category', width, catBtn);
	}

	// Checkout continue (guest with cart)
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	const pHref = await page.locator('a[href*="/product/"]').first().getAttribute('href');
	await page.goto(pHref.startsWith('http') ? pHref : `${SITE}${pHref}`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page);
	await page.evaluate(() => {
		document.querySelector('.wd-swatches-product .wd-swatch:not(.wd-disabled)')?.click();
	});
	await page.locator('.single_add_to_cart_button').click({ force: true }).catch(() => {});
	await page.waitForTimeout(1000);
	await page.goto(`${SITE}/cart/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await lightPrep(page);
	const checkoutBtn = page.locator('a.checkout-button, .wc-proceed-to-checkout a, a.checkout').first();
	if (await checkoutBtn.count()) {
		await checkoutBtn.hover({ force: true });
		await page.waitForTimeout(200);
		await saveLocator(page, 'button-hover-checkout', width, checkoutBtn);
		await checkoutBtn.focus();
		await saveLocator(page, 'button-focus-checkout', width, checkoutBtn);
	}

	// Nav + footer link hover
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await lightPrep(page);
	const navLink = page.locator('header .wd-nav-main a, header nav a').filter({ hasText: /مردانه|فروشگاه/ }).first();
	if (await navLink.count()) {
		await navLink.hover({ force: true });
		await saveLocator(page, 'link-hover-nav', width, navLink);
	}
	await page.evaluate(() => document.querySelector('footer.bgf, footer')?.scrollIntoView());
	await page.waitForTimeout(300);
	const footLink = page.locator('footer.bgf a, footer a').filter({ hasText: /درباره|تماس|فروشگاه/ }).first();
	if (await footLink.count()) {
		await footLink.hover({ force: true });
		await saveLocator(page, 'link-hover-footer', width, footLink);
	}

	// Keep legacy ids as composites pointing to primary ATC (for pages.md compatibility)
	for (const [from, to] of [
		['button-hover', 'button-hover-atc'],
		['button-focus', 'button-focus-atc'],
	]) {
		const src = path.join(OUT, to, `${width}.png`);
		const destDir = path.join(OUT, from);
		fs.mkdirSync(destDir, { recursive: true });
		fs.copyFileSync(src, path.join(destDir, `${width}.png`));
	}

	await ctx.close();
}

// ——— F14: shadows + transitions without transition:none ———
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'fa-IR' });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await lightPrep(page); // does NOT kill transitions
	await page.waitForTimeout(500);
	const measures = await page.evaluate(() => {
		const pick = (sel) => document.querySelector(sel);
		const styleOf = (el) => {
			if (!el) return null;
			const cs = getComputedStyle(el);
			return {
				boxShadow: cs.boxShadow,
				transition: cs.transition,
				transitionDuration: cs.transitionDuration,
				transitionTimingFunction: cs.transitionTimingFunction,
				fontFamily: cs.fontFamily,
			};
		};
		const card = pick('.wd-product, .product-grid-item, li.product');
		const btn = pick('.button, .btn, a.button');
		const out = {
			cardRest: styleOf(card),
			btnRest: styleOf(btn),
			fonts: {
				body: styleOf(document.body)?.fontFamily,
				nav: styleOf(pick('header .wd-nav-main a, header nav a')),
				price: styleOf(pick('.price, .amount')),
				heading: styleOf(pick('h1, .entry-title')),
				button: styleOf(btn),
			},
		};
		return out;
	});
	// hover card
	const cardLoc = page.locator('.wd-product, .product-grid-item, li.product').first();
	if (await cardLoc.count()) {
		await cardLoc.hover({ force: true });
		await page.waitForTimeout(400);
		measures.cardHover = await page.evaluate(() => {
			const el = document.querySelector('.wd-product, .product-grid-item, li.product');
			const cs = getComputedStyle(el);
			return { boxShadow: cs.boxShadow, transition: cs.transition, transitionDuration: cs.transitionDuration };
		});
	}
	// quick view / drawer / menu shadows from home
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await lightPrep(page);
	await page.locator('header .wd-nav-main a, header nav a').filter({ hasText: /^زنانه$/ }).first().hover({ force: true });
	await page.waitForTimeout(500);
	measures.menu = await page.evaluate(() => {
		const sub = document.querySelector('.wd-dropdown-menu, .wd-dropdown, .sub-menu');
		if (!sub) return null;
		const cs = getComputedStyle(sub);
		return { boxShadow: cs.boxShadow, transition: cs.transition, transitionDuration: cs.transitionDuration };
	});
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await lightPrep(page);
	const qv = page.locator('.wd-product .open-quick-view, .wd-product a.quick-view').first();
	if (await qv.count()) {
		await qv.click({ force: true }).catch(() => {});
		await page.waitForTimeout(1000);
		measures.quickView = await page.evaluate(() => {
			const el = document.querySelector('.mfp-content, .wd-popup, .wd-quick-view-wrapper');
			if (!el) return null;
			const cs = getComputedStyle(el);
			return { boxShadow: cs.boxShadow, transition: cs.transition };
		});
		await page.keyboard.press('Escape').catch(() => {});
	}
	// mobile drawer shadow
	await page.setViewportSize({ width: 390, height: 844 });
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await lightPrep(page);
	await page.evaluate(() => document.querySelector('.mobile-nav-icon, .wd-header-mobile-nav a, .wd-burger')?.click());
	await page.waitForTimeout(700);
	measures.drawer = await page.evaluate(() => {
		const el = document.querySelector('.mobile-nav, .wd-side-hidden, .wd-nav-mobile');
		if (!el) return null;
		const cs = getComputedStyle(el);
		return { boxShadow: cs.boxShadow, transition: cs.transition, transitionDuration: cs.transitionDuration };
	});

	fs.writeFileSync(path.join(REPORT, 'f14-motion-shadows.json'), JSON.stringify(measures, null, 2));
	results.f14 = measures;

	// Merge into _tokens.json
	const tokensPath = path.join(SPECS, '_tokens.json');
	const tokens = JSON.parse(fs.readFileSync(tokensPath, 'utf8'));
	const shadows = new Set(tokens.boxShadows || []);
	const transitions = new Set((tokens.transitions || []).filter((t) => t && t !== 'none' && t !== 'all 0s ease 0s'));
	for (const key of ['cardRest', 'cardHover', 'btnRest', 'menu', 'quickView', 'drawer']) {
		const m = measures[key];
		if (!m) continue;
		if (m.boxShadow && m.boxShadow !== 'none') shadows.add(m.boxShadow);
		if (m.transition && m.transition !== 'none' && m.transition !== 'all 0s ease 0s') transitions.add(m.transition);
	}
	tokens.boxShadows = [...shadows];
	tokens.transitions = [...transitions];
	tokens.fontUsage = {
		note: 'هر دو فونت تجاری‌اند؛ فاز ۲ باید جایگزین (Vazirmatn + آپلود سفارشی) داشته باشد.',
		iranyekan: 'عناصر UI عمومی / دکمه‌ها / متن بدنه (نمونه: button, body در اندازه‌گیری)',
		IRANSansXFaNum: 'متن‌ها و قیمت‌ها با ارقام فارسی (نمونه: price, heading, nav — خانواده IRANSansXFaNum)',
		samples: measures.fonts,
	};
	tokens.generatedAt = new Date().toISOString();
	tokens.f14Source = 'qa/reports/phase-1/f14-motion-shadows.json (بدون transition:none)';
	fs.writeFileSync(tokensPath, JSON.stringify(tokens, null, 2));
	console.log('tokens shadows', tokens.boxShadows.length, 'transitions', tokens.transitions.length);

	await ctx.close();
}

fs.writeFileSync(path.join(REPORT, 'r3-results.json'), JSON.stringify(results, null, 2));
console.log('HASHES', {
	mega: results.megaFile,
	search: results.searchFile,
	sticky: results.stickyFile,
	mini: results.miniFile,
});
await browser.close();
console.log('R3_DONE');
