/**
 * Interactive state baselines (F3) — resilient version.
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { preparePage, shotLocator, shotFull, KEY_SELECTORS, STYLE_PROPS } from './lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });
const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const OUT = path.join(__dirname, 'states');
const SPECS = path.join(__dirname, 'specs');
const REPORT = path.join(root, 'qa/reports/phase-1');
fs.mkdirSync(OUT, { recursive: true });

async function saveState(id, width, page, locator) {
	const dir = path.join(OUT, id);
	fs.mkdirSync(dir, { recursive: true });
	const file = path.join(dir, `${width}.png`);
	let ok = false;
	if (locator) ok = await shotLocator(page, locator, file);
	else {
		await shotFull(page, file, width);
		ok = fs.existsSync(file);
	}
	const specs = await page.evaluate(
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
					bbox: { x: Math.round(rect.x), y: Math.round(rect.y), width: Math.round(rect.width), height: Math.round(rect.height) },
					style,
				};
			}
			return out;
		},
		{ selectors: KEY_SELECTORS, props: STYLE_PROPS, pageId: `state-${id}`, width }
	);
	fs.writeFileSync(path.join(SPECS, `state-${id}-${width}.json`), JSON.stringify(specs, null, 2));
	return ok;
}

const manifest = [];
const browser = await chromium.launch({ headless: true });

{
	const width = 1440;
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR', reducedMotion: 'reduce' });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await preparePage(page);

	// Mega menu via JS class/hover dispatch
	await page.evaluate(() => {
		const link = [...document.querySelectorAll('header a')].find((a) => a.textContent.trim() === 'زنانه');
		const li = link?.closest('li');
		if (li) {
			li.classList.add('focus', 'hover', 'opened');
			li.dispatchEvent(new Event('mouseenter', { bubbles: true }));
			const sub = li.querySelector('.sub-menu, .wd-dropdown, .wd-dropdown-menu');
			if (sub) sub.style.display = 'block';
		}
	});
	await page.waitForTimeout(400);
	manifest.push({ id: 'mega-menu-open', width, ok: await saveState('mega-menu-open', width, page, page.locator('header')) });

	// Search open
	await page.evaluate(() => {
		const btn = document.querySelector('.wd-header-search a, .wd-tools-element.wd-header-search a');
		btn?.click();
	});
	await page.waitForTimeout(500);
	manifest.push({ id: 'search-open', width, ok: await saveState('search-open', width, page, page.locator('header')) });
	await page.keyboard.press('Escape').catch(() => {});

	await page.evaluate(() => window.scrollTo(0, 900));
	await page.waitForTimeout(500);
	manifest.push({ id: 'sticky-header', width, ok: await saveState('sticky-header', width, page, page.locator('header')) });

	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await preparePage(page);
	await page.evaluate(() => {
		const card = document.querySelector('.wd-product, .product');
		if (card) {
			card.classList.add('hover');
			card.dispatchEvent(new Event('mouseenter', { bubbles: true }));
		}
	});
	await page.waitForTimeout(300);
	manifest.push({ id: 'product-card-hover', width, ok: await saveState('product-card-hover', width, page, page.locator('.wd-product, .product').first()) });

	const qv = page.locator('.wd-product .open-quick-view, .wd-product a.quick-view, .wd-product .wd-quick-view').first();
	if (await qv.count()) {
		await qv.click({ force: true, timeout: 5000 }).catch(() => {});
		await page.waitForTimeout(1200);
		manifest.push({
			id: 'quick-view-modal',
			width,
			ok: await saveState('quick-view-modal', width, page, page.locator('.mfp-content, .wd-popup, .pswp, .wd-quick-view-wrapper').first()),
		});
		await page.keyboard.press('Escape').catch(() => {});
	}

	await page.goto(`${SITE}/contact-us/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await preparePage(page);
	const btn = page.locator('button, .button, input[type=submit]').first();
	if (await btn.count()) {
		await btn.focus();
		await page.waitForTimeout(200);
		manifest.push({ id: 'button-focus', width, ok: await saveState('button-focus', width, page, btn) });
		await btn.hover({ force: true }).catch(() => {});
		manifest.push({ id: 'button-hover', width, ok: await saveState('button-hover', width, page, btn) });
	}
	const input = page.locator('input[type=text], input[type=email], textarea').first();
	if (await input.count()) {
		await input.focus();
		manifest.push({ id: 'input-focus', width, ok: await saveState('input-focus', width, page, input) });
	}

	const productUrl = await page.evaluate(async () => {
		const r = await fetch('/shop/');
		return null;
	}).catch(() => null);
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	const href = await page.locator('a[href*="/product/"]').first().getAttribute('href');
	if (href) {
		await page.goto(href, { waitUntil: 'domcontentloaded', timeout: 90000 });
		await preparePage(page);
		await page.locator('.wd-swatches-product .wd-swatch').first().click({ force: true }).catch(() => {});
		await page.waitForTimeout(400);
		manifest.push({ id: 'product-swatch', width, ok: await saveState('product-swatch', width, page, page.locator('.summary, .entry-summary, .product-image-summary').first()) });
		const tab = page.locator('.wc-tabs li a, .woocommerce-tabs li a').nth(1);
		if (await tab.count()) {
			await tab.click({ force: true });
			await page.waitForTimeout(300);
			manifest.push({ id: 'product-tabs', width, ok: await saveState('product-tabs', width, page, page.locator('.woocommerce-tabs, .wd-tabs').first()) });
		}
		await page.locator('.single_add_to_cart_button').click({ force: true }).catch(() => {});
		await page.waitForTimeout(1200);
		await page.evaluate(() => {
			document.querySelector('.wd-header-cart a, .cart-contents')?.click();
		});
		await page.waitForTimeout(800);
		manifest.push({
			id: 'mini-cart-open',
			width,
			ok: await saveState('mini-cart-open', width, page, page.locator('.cart-widget-side, .wd-side-cart, .widget_shopping_cart, .wd-dropdown-cart').first()),
		});
	}
	await ctx.close();
}

{
	const width = 390;
	const ctx = await browser.newContext({
		viewport: { width, height: 844 },
		locale: 'fa-IR',
		reducedMotion: 'reduce',
		isMobile: true,
		hasTouch: true,
	});
	const page = await ctx.newPage();
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await preparePage(page);
	await page.evaluate(() => {
		document.querySelector('.mobile-nav-icon, .wd-header-mobile-nav a, .wd-burger')?.click();
	});
	await page.waitForTimeout(700);
	manifest.push({
		id: 'mobile-drawer-open',
		width,
		ok: await saveState('mobile-drawer-open', width, page, page.locator('.mobile-nav, .wd-side-hidden, .wd-nav-mobile').first()),
	});
	await ctx.close();
}

fs.writeFileSync(path.join(REPORT, 'states-manifest.json'), JSON.stringify(manifest, null, 2));
console.log('states', manifest);
await browser.close();
