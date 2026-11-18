/** Capture product-sample + guest cart-full + checkout (F4). */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { preparePage, collectSpecs, shotFull } from './lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });
const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const WIDTHS = [1440, 768, 390];
const SPECS = path.join(__dirname, 'specs');
const REPORT = path.join(root, 'qa/reports/phase-1');
fs.mkdirSync(SPECS, { recursive: true });

const browser = await chromium.launch({ headless: true });
const ctx0 = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'fa-IR', reducedMotion: 'reduce' });
const p0 = await ctx0.newPage();
await p0.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
await p0.waitForTimeout(1500);
const productUrl = await p0.evaluate(() => {
	const a = document.querySelector('a[href*="/product/"]');
	return a ? a.href : null;
});
console.log('productUrl', productUrl);
await ctx0.close();
if (!productUrl) {
	console.error('no product');
	process.exit(1);
}

const results = [];
for (const width of WIDTHS) {
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR', reducedMotion: 'reduce' });
	const page = await ctx.newPage();
	await page.goto(productUrl, { waitUntil: 'domcontentloaded', timeout: 90000 });
	const prep = await preparePage(page);
	const dir = path.join(__dirname, 'product-sample');
	await shotFull(page, path.join(dir, `${width}.png`), width);
	fs.writeFileSync(path.join(SPECS, `product-sample-${width}.json`), JSON.stringify(await collectSpecs(page, 'product-sample', width), null, 2));
	if (width === 1440 || width === 1280 || width === 1024) {
		/* header/footer optional */
	}
	results.push({ id: 'product-sample', width, incomplete: prep.imgStats.incomplete });
	console.log('product-sample', width, prep.imgStats);

	// add to cart
	await page.locator('.wd-swatches-product .wd-swatch').first().click({ timeout: 3000 }).catch(() => {});
	await page.waitForTimeout(300);
	await page.locator('.single_add_to_cart_button').click({ timeout: 5000 }).catch(() => {});
	await page.waitForTimeout(1500);

	await page.goto(`${SITE}/cart/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	const prep2 = await preparePage(page);
	await shotFull(page, path.join(__dirname, 'cart-full', `${width}.png`), width);
	fs.writeFileSync(path.join(SPECS, `cart-full-${width}.json`), JSON.stringify(await collectSpecs(page, 'cart-full', width), null, 2));
	results.push({ id: 'cart-full', width, incomplete: prep2.imgStats.incomplete });
	console.log('cart-full', width, prep2.imgStats);

	await page.goto(`${SITE}/checkout/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	const prep3 = await preparePage(page);
	await shotFull(page, path.join(__dirname, 'checkout', `${width}.png`), width);
	fs.writeFileSync(path.join(SPECS, `checkout-${width}.json`), JSON.stringify(await collectSpecs(page, 'checkout', width), null, 2));
	results.push({ id: 'checkout', width, incomplete: prep3.imgStats.incomplete });
	console.log('checkout', width, prep3.imgStats);

	if (width === 1440) {
		await page.locator('#place_order, button[name="woocommerce_checkout_place_order"]').click({ timeout: 5000 }).catch(() => {});
		await page.waitForTimeout(1500);
		await preparePage(page);
		const errDir = path.join(__dirname, 'states', 'checkout-validation');
		fs.mkdirSync(errDir, { recursive: true });
		await shotFull(page, path.join(errDir, '1440.png'), width);
		console.log('checkout-validation saved');
	}
	await ctx.close();
}

// HF for product
for (const width of [1280, 1024]) {
	const ctx = await browser.newContext({ viewport: { width, height: 900 }, locale: 'fa-IR', reducedMotion: 'reduce' });
	const page = await ctx.newPage();
	await page.goto(productUrl, { waitUntil: 'domcontentloaded', timeout: 90000 });
	await preparePage(page, { light: true });
	const dir = path.join(__dirname, 'product-sample');
	const { shotLocator } = await import('./lib.mjs');
	await shotLocator(page, page.locator('header'), path.join(dir, `header-${width}.png`));
	await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
	await page.waitForTimeout(400);
	await shotLocator(page, page.locator('footer'), path.join(dir, `footer-${width}.png`));
	await ctx.close();
	console.log('product HF', width);
}

fs.writeFileSync(path.join(REPORT, 'cart-product-run.json'), JSON.stringify({ productUrl, results }, null, 2));
await browser.close();
console.log('Done cart-product');
