/**
 * Phase 1 baseline capture (R2): lazy-load aware, exact viewport width, expanded specs.
 * Usage: npm run qa:baseline
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import {
	KEY_SELECTORS,
	preparePage,
	collectSpecs,
	shotFull,
	shotLocator,
	buildTokens,
} from './lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });

const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const pagesConfig = JSON.parse(fs.readFileSync(path.join(__dirname, 'pages.json'), 'utf8'));
const WIDTHS = pagesConfig.widths;
const HF_WIDTHS = pagesConfig.headerFooterWidths;
const OUT = __dirname;
const SPECS = path.join(__dirname, 'specs');
const REPORT = path.join(root, 'qa/reports/phase-1');
const META = path.join(REPORT, 'dimensions.json');

fs.mkdirSync(SPECS, { recursive: true });
fs.mkdirSync(REPORT, { recursive: true });

async function resolveExtras(browser) {
	const ctx = await browser.newContext({
		viewport: { width: 1440, height: 900 },
		locale: 'fa-IR',
		reducedMotion: 'reduce',
	});
	const page = await ctx.newPage();
	const out = { products: {}, legal: [], blog: null, overflowNotes: [] };
	try {
		await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
		await page.waitForTimeout(1000);
		out.products = await page.evaluate(() => {
			const cards = [...document.querySelectorAll('.wd-product, li.product, .product')];
			const href = (c) => {
				if (!c) return null;
				return c.querySelector('a[href*="/product/"]')?.href || null;
			};
			return {
				any: href(cards[0]),
				sale: href(cards.find((c) => c.querySelector('.onsale, .wd-label-sale, .wd-product-labels'))),
				outofstock: href(cards.find((c) => /outofstock|out-of-stock/.test(c.className))),
				variable: href(
					cards.find(
						(c) =>
							/product-type-variable|product_type_variable/.test(c.className) ||
							!!c.querySelector('.wd-swatches, .variations_form')
					)
				),
			};
		});

		// Legal links from footer
		await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
		const links = await page.evaluate(() =>
			[...document.querySelectorAll('footer a')].map((a) => ({ t: a.innerText.trim(), h: a.href }))
		);
		out.legal = links.filter((l) => /قانون|مقررات|همکاری|affiliate|terms|privacy|حریم/i.test(`${l.t} ${l.h}`));

		// Blog presence
		const blogRes = await page.goto(`${SITE}/blog/`, { waitUntil: 'domcontentloaded', timeout: 30000 }).catch(() => null);
		out.blog = {
			url: `${SITE}/blog/`,
			status: blogRes ? blogRes.status() : 'error',
			hasPosts: blogRes
				? await page.evaluate(() => !!document.querySelector('article, .post, .blog-post-loop'))
				: false,
		};
	} catch (e) {
		out.error = e.message;
	}
	await ctx.close();
	return out;
}

async function capturePage(browser, entry, width, dimensions) {
	const isFull = WIDTHS.includes(width);
	const isHf = HF_WIDTHS.includes(width);
	const context = await browser.newContext({
		viewport: { width, height: 900 },
		locale: 'fa-IR',
		reducedMotion: 'reduce',
		extraHTTPHeaders: { 'Accept-Language': 'fa-IR,fa;q=0.9' },
	});
	const page = await context.newPage();
	const dir = path.join(OUT, entry.id);
	fs.mkdirSync(dir, { recursive: true });
	const url = entry.url.startsWith('http') ? entry.url : `${SITE}${entry.url}`;
	let status = 'ok';
	let imgIncomplete = null;
	try {
		if (url.endsWith('#') || url.includes('/#') || entry.url === '#') {
			await context.close();
			return { id: entry.id, width, status: 'skip-hash-link', url };
		}
		const resp = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 90000 });
		if (entry.optional && resp && resp.status() >= 400) {
			status = `skip-http-${resp.status()}`;
			await context.close();
			return { id: entry.id, width, status, url };
		}
		if (!resp) status = 'no-response';
		else if (resp.status() >= 400 && entry.id !== '404') status = `http-${resp.status()}`;

		const prep = await preparePage(page, {
			maskSelectors: pagesConfig.maskSelectors,
			light: !isFull, // header/footer crops: lighter wait
		});
		imgIncomplete = prep.imgStats;
		if (isFull && prep.overflow.scrollWidth > prep.overflow.clientWidth + 2) {
			const offenders = await page.evaluate(() => {
				const docW = document.documentElement.clientWidth;
				return [...document.querySelectorAll('body *')]
					.filter((el) => el.getBoundingClientRect().right > docW + 2)
					.slice(0, 8)
					.map((el) => ({
						tag: el.tagName,
						class: (el.className || '').toString().slice(0, 80),
						right: Math.round(el.getBoundingClientRect().right),
					}));
			});
			fs.writeFileSync(
				path.join(REPORT, `overflow-${entry.id}-${width}.json`),
				JSON.stringify({ overflow: prep.overflow, offenders }, null, 2)
			);
		}

		if (isFull) {
			const dim = await shotFull(page, path.join(dir, `${width}.png`), width);
			dimensions.push({
				page: entry.id,
				file: `${entry.id}/${width}.png`,
				...dim,
				imgIncomplete: imgIncomplete?.incomplete ?? null,
			});
			const specs = await collectSpecs(page, entry.id, width);
			fs.writeFileSync(path.join(SPECS, `${entry.id}-${width}.json`), JSON.stringify(specs, null, 2));
		}

		if (isHf) {
			await shotLocator(page, page.locator('header.whb-header, header'), path.join(dir, `header-${width}.png`));
			await page.evaluate(() => {
				const f = document.querySelector('footer.bgf, footer.wd-footer, footer');
				f?.scrollIntoView({ block: 'start' });
			});
			await page.waitForTimeout(400);
			// footer.wd-footer روی سایت خالی (ارتفاع ۰) است؛ فوتر واقعی footer.bgf است.
			const footerLoc = page.locator('footer.bgf, footer.wd-footer, footer');
			let footerOk = await shotLocator(page, footerLoc, path.join(dir, `footer-${width}.png`));
			if (!footerOk) {
				const box = await page.locator('footer.bgf').first().boundingBox().catch(() => null);
				if (box && box.height > 10) {
					const prev = page.viewportSize();
					await page.setViewportSize({ width, height: Math.min(Math.ceil(box.height) + 80, 4000) });
					await page.evaluate(() => document.querySelector('footer.bgf')?.scrollIntoView({ block: 'start' }));
					await page.waitForTimeout(120);
					const box2 = await page.locator('footer.bgf').first().boundingBox();
					if (box2?.height > 10) {
						await page.screenshot({
							path: path.join(dir, `footer-${width}.png`),
							animations: 'disabled',
							clip: { x: 0, y: Math.max(0, Math.floor(box2.y)), width, height: Math.ceil(box2.height) },
						});
						footerOk = true;
					}
					if (prev) await page.setViewportSize(prev);
				}
			}
			dimensions.push({ page: entry.id, file: `${entry.id}/header-${width}.png`, kind: 'header', width });
			dimensions.push({ page: entry.id, file: `${entry.id}/footer-${width}.png`, kind: 'footer', width });
		}
	} catch (e) {
		status = `error: ${e.message}`;
	}
	await context.close();
	return { id: entry.id, width, status, url, imgIncomplete };
}

async function captureGuestCartCheckout(browser, productUrl, dimensions, results) {
	if (!productUrl) return;
	const widths = WIDTHS;
	for (const width of widths) {
		const context = await browser.newContext({
			viewport: { width, height: 900 },
			locale: 'fa-IR',
			reducedMotion: 'reduce',
		});
		const page = await context.newPage();
		try {
			await page.goto(productUrl, { waitUntil: 'domcontentloaded', timeout: 90000 });
			await preparePage(page, { maskSelectors: pagesConfig.maskSelectors });
			// Simple or variable: try add to cart
			const added = await page.evaluate(() => {
				const swatch = document.querySelector('.wd-swatches-product .wd-swatch:not(.wd-disabled), .variations select');
				if (swatch && swatch.tagName === 'DIV') swatch.click();
				if (swatch && swatch.tagName === 'SELECT' && swatch.options.length > 1) {
					swatch.selectedIndex = 1;
					swatch.dispatchEvent(new Event('change', { bubbles: true }));
				}
				return true;
			});
			await page.waitForTimeout(400);
			const btn = page.locator('.single_add_to_cart_button, button.alt').first();
			if (await btn.count()) {
				await btn.click({ timeout: 5000 }).catch(() => {});
				await page.waitForTimeout(1500);
			}

			await page.goto(`${SITE}/cart/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
			await preparePage(page, { maskSelectors: pagesConfig.maskSelectors });
			const dirCart = path.join(OUT, 'cart-full');
			const dim = await shotFull(page, path.join(dirCart, `${width}.png`), width);
			dimensions.push({ page: 'cart-full', file: `cart-full/${width}.png`, ...dim });
			fs.writeFileSync(
				path.join(SPECS, `cart-full-${width}.json`),
				JSON.stringify(await collectSpecs(page, 'cart-full', width), null, 2)
			);
			results.push({ id: 'cart-full', width, status: 'ok' });

			await page.goto(`${SITE}/checkout/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
			await preparePage(page, { maskSelectors: pagesConfig.maskSelectors });
			const dirCo = path.join(OUT, 'checkout');
			const dim2 = await shotFull(page, path.join(dirCo, `${width}.png`), width);
			dimensions.push({ page: 'checkout', file: `checkout/${width}.png`, ...dim2 });
			fs.writeFileSync(
				path.join(SPECS, `checkout-${width}.json`),
				JSON.stringify(await collectSpecs(page, 'checkout', width), null, 2)
			);
			results.push({ id: 'checkout', width, status: 'ok' });

			// Validation errors (no order): submit empty
			if (width === 1440) {
				await page.locator('#place_order, button[name="woocommerce_checkout_place_order"]').click({ timeout: 5000 }).catch(() => {});
				await page.waitForTimeout(1200);
				await preparePage(page, { maskSelectors: pagesConfig.maskSelectors });
				const errDir = path.join(OUT, 'states', 'checkout-validation');
				await shotFull(page, path.join(errDir, '1440.png'), width);
				const notice = page.locator('.woocommerce-error, .woocommerce-NoticeGroup');
				await shotLocator(page, notice, path.join(errDir, 'notice-1440.png'));
				results.push({ id: 'checkout-validation', width, status: 'ok' });
			}
		} catch (e) {
			results.push({ id: 'cart-checkout', width, status: e.message });
		}
		await context.close();
	}
}

async function main() {
	const browser = await chromium.launch({ headless: true });
	const extras = await resolveExtras(browser);
	fs.writeFileSync(path.join(REPORT, 'resolved-extras.json'), JSON.stringify(extras, null, 2));

	const pages = [...pagesConfig.pages];
	if (extras.products?.any) pages.push({ id: 'product-sample', url: extras.products.any, title: 'محصول نمونه' });
	if (extras.products?.variable && extras.products.variable !== extras.products.any) {
		pages.push({ id: 'product-variable', url: extras.products.variable, title: 'محصول متغیر' });
	}
	if (extras.products?.sale) pages.push({ id: 'product-sale', url: extras.products.sale, title: 'تخفیف‌دار' });
	if (extras.products?.outofstock) {
		pages.push({ id: 'product-outofstock', url: extras.products.outofstock, title: 'ناموجود' });
	}
	// Footer legal links are currently href="#" — documented in inventory; no page capture.

	const results = [];
	const dimensions = [];
	const hfPages = new Set(['home', 'shop', 'product-sample']);
	for (const entry of pages) {
		const widths = hfPages.has(entry.id)
			? [...new Set([...WIDTHS, ...HF_WIDTHS])]
			: [...WIDTHS];
		for (const width of widths) {
			process.stdout.write(`capture ${entry.id}@${width} ... `);
			const r = await capturePage(browser, entry, width, dimensions);
			console.log(
				r.status,
				r.imgIncomplete ? `imgIncomplete=${r.imgIncomplete.incomplete}` : ''
			);
			results.push(r);
		}
	}

	await captureGuestCartCheckout(browser, extras.products?.any, dimensions, results);

	// Tokens summary
	const specFiles = fs.readdirSync(SPECS).filter((f) => f.endsWith('.json') && !f.startsWith('_')).map((f) => path.join(SPECS, f));
	const tokens = buildTokens(specFiles);
	fs.writeFileSync(path.join(SPECS, '_tokens.json'), JSON.stringify(tokens, null, 2));

	const incompleteTotal = results.reduce((n, r) => n + (r.imgIncomplete?.incomplete || 0), 0);
	fs.writeFileSync(META, JSON.stringify(dimensions, null, 2));
	fs.writeFileSync(
		path.join(REPORT, 'baseline-run.json'),
		JSON.stringify({ site: SITE, results, extras, incompleteTotal, finishedAt: new Date().toISOString() }, null, 2)
	);
	console.log(`Done. incompleteImgsSum=${incompleteTotal} dims=${dimensions.length}`);
	await browser.close();
	if (incompleteTotal > 0) process.exitCode = 1;
}

main().catch((e) => {
	console.error(e);
	process.exit(1);
});
