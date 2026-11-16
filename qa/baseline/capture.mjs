/**
 * Phase 1 baseline capture for bagllet.ir (live Woodmart site).
 * Usage: npm run qa:baseline
 * Env: SITE_URL from .env.local (fallback https://bagllet.ir)
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });

const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const pagesConfig = JSON.parse(
	fs.readFileSync(path.join(__dirname, 'pages.json'), 'utf8')
);
const WIDTHS = pagesConfig.widths;
const HF_WIDTHS = pagesConfig.headerFooterWidths;
const OUT = path.join(__dirname);
const SPECS = path.join(__dirname, 'specs');
const REPORT = path.join(root, 'qa/reports/phase-1');

fs.mkdirSync(SPECS, { recursive: true });
fs.mkdirSync(REPORT, { recursive: true });

const STYLE_PROPS = [
	'fontFamily',
	'fontSize',
	'fontWeight',
	'lineHeight',
	'letterSpacing',
	'color',
	'backgroundColor',
	'paddingTop',
	'paddingRight',
	'paddingBottom',
	'paddingLeft',
	'marginTop',
	'marginRight',
	'marginBottom',
	'marginLeft',
	'borderTopWidth',
	'borderRightWidth',
	'borderBottomWidth',
	'borderLeftWidth',
	'borderTopColor',
	'borderRadius',
	'boxShadow',
	'transition',
	'zIndex',
	'display',
	'width',
	'height',
	'maxWidth',
	'gap',
];

const KEY_SELECTORS = {
	header: 'header.whb-header, header',
	topBar: '.whb-top-bar, .whb-row-top, .topbar-contents',
	logo: '.wd-logo, .site-logo, .whb-header .wd-header-logo',
	mainNav: '.wd-nav.wd-nav-main, .main-nav, nav.wd-header-main-nav',
	search: '.wd-header-search, .searchform',
	cart: '.wd-header-cart, .woodmart-shopping-cart',
	wishlist: '.wd-header-wishlist',
	footer: 'footer.wd-footer, footer',
	productCard: '.wd-product, .product-grid-item',
	addToCartBtn: '.single_add_to_cart_button, .add_to_cart_button, .button.product_type_simple',
	price: '.price, .wd-entities-title + .price, .amount',
	heading: 'h1, .entry-title, .wd-page-title',
	buttonPrimary: '.btn, .button, button.button',
};

async function collectSpecs(page, pageId, width) {
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
		{ selectors: KEY_SELECTORS, props: STYLE_PROPS, pageId, width }
	);
}

async function resolveProductUrls(page) {
	await page.goto(`${SITE}/shop/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	await page.waitForTimeout(1500);
	return page.evaluate(() => {
		const cards = [...document.querySelectorAll('.wd-product, .product')];
		const pick = (pred) => {
			const c = cards.find(pred);
			const a = c?.querySelector('a[href*="/product/"]');
			return a ? a.href : null;
		};
		return {
			any: pick(() => true),
			sale: pick((c) => c.querySelector('.onsale, .wd-label-sale, .wd-product-labels')),
			outofstock: pick((c) =>
				/outofstock|out-of-stock/.test(c.className) ||
				!!c.querySelector('.out-of-stock')
			),
			variable: pick((c) =>
				/product-type-variable|product_type_variable/.test(c.className) ||
				!!c.querySelector('.variations_form, .wd-swatches')
			),
		};
	});
}

async function capturePage(browser, entry, width) {
	const context = await browser.newContext({
		viewport: { width, height: 900 },
		locale: 'fa-IR',
		extraHTTPHeaders: { 'Accept-Language': 'fa-IR,fa;q=0.9' },
	});
	const page = await context.newPage();
	const dir = path.join(OUT, entry.id);
	fs.mkdirSync(dir, { recursive: true });

	const url = entry.url.startsWith('http') ? entry.url : `${SITE}${entry.url}`;
	let status = 'ok';
	try {
		const resp = await page.goto(url, { waitUntil: 'networkidle', timeout: 90000 });
		if (!resp) status = 'no-response';
		else if (resp.status() >= 400 && entry.id !== '404') status = `http-${resp.status()}`;
		await page.waitForTimeout(1200);

		// Hide admin bar for cleaner baseline when cookies present
		await page.addStyleTag({
			content: '#wpadminbar{display:none!important} html{margin-top:0!important}',
		});

		for (const sel of pagesConfig.maskSelectors) {
			await page.evaluate((s) => {
				document.querySelectorAll(s).forEach((el) => {
					el.style.visibility = 'hidden';
				});
			}, sel).catch(() => {});
		}

		const shotPath = path.join(dir, `${width}.png`);
		await page.screenshot({ path: shotPath, fullPage: true });

		if (WIDTHS.includes(width)) {
			const specs = await collectSpecs(page, entry.id, width);
			fs.writeFileSync(
				path.join(SPECS, `${entry.id}-${width}.json`),
				JSON.stringify(specs, null, 2),
				'utf8'
			);
		}

		if (HF_WIDTHS.includes(width) || WIDTHS.includes(width)) {
			const header = page.locator('header.whb-header, header').first();
			if (await header.count()) {
				await header.screenshot({ path: path.join(dir, `header-${width}.png`) }).catch(() => {});
			}
			const footer = page.locator('footer.wd-footer, footer').first();
			if (await footer.count()) {
				await footer.screenshot({ path: path.join(dir, `footer-${width}.png`) }).catch(() => {});
			}
		}
	} catch (e) {
		status = `error: ${e.message}`;
	}

	await context.close();
	return { id: entry.id, width, status, url };
}

async function main() {
	const browser = await chromium.launch({ headless: true });
	const results = [];

	// Resolve live product URLs
	const tmp = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const p = await tmp.newPage();
	let products = {};
	try {
		products = await resolveProductUrls(p);
	} catch (e) {
		products = { error: e.message };
	}
	await tmp.close();
	fs.writeFileSync(path.join(REPORT, 'resolved-products.json'), JSON.stringify(products, null, 2));

	const extra = [];
	if (products.any) {
		extra.push({ id: 'product-sample', url: products.any, title: 'محصول نمونه' });
	}
	if (products.variable) {
		extra.push({ id: 'product-variable', url: products.variable, title: 'محصول متغیر' });
	}
	if (products.sale) {
		extra.push({ id: 'product-sale', url: products.sale, title: 'محصول تخفیف‌دار' });
	}
	if (products.outofstock) {
		extra.push({ id: 'product-outofstock', url: products.outofstock, title: 'محصول ناموجود' });
	}

	const allPages = [
		...pagesConfig.pages.filter((x) => x.id !== 'product-clutch'),
		...extra,
	];

	for (const entry of allPages) {
		for (const width of [...new Set([...WIDTHS, ...HF_WIDTHS])]) {
			process.stdout.write(`capture ${entry.id}@${width} ... `);
			const r = await capturePage(browser, entry, width);
			console.log(r.status);
			results.push(r);
		}
	}

	// Breakpoints observe-only dump from home
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	await page.goto(`${SITE}/`, { waitUntil: 'domcontentloaded', timeout: 60000 });
	const mq = await page.evaluate(() => {
		const set = new Set();
		for (const sheet of document.styleSheets) {
			try {
				for (const rule of sheet.cssRules) {
					if (rule.media && rule.media.mediaText) set.add(rule.media.mediaText);
				}
			} catch (_) {}
		}
		return [...set].sort();
	});
	fs.writeFileSync(path.join(SPECS, '_breakpoints.json'), JSON.stringify({ site: SITE, mediaQueries: mq }, null, 2));
	await ctx.close();

	fs.writeFileSync(path.join(REPORT, 'baseline-run.json'), JSON.stringify({ site: SITE, results, products }, null, 2));
	const ok = results.filter((r) => r.status === 'ok').length;
	console.log(`Done: ${ok}/${results.length} ok`);
	await browser.close();
	if (ok / results.length < 0.8) process.exitCode = 1;
}

main().catch((e) => {
	console.error(e);
	process.exit(1);
});
