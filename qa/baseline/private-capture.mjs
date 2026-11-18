/**
 * Capture logged-in account pages into qa/baseline-private/ (gitignored).
 * Prefers qa/.auth/storage.json from `npm run qa:login`.
 * Fallback: runs headed login prompt if storage missing.
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { preparePage, shotFull } from './lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });
const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const AUTH = path.join(root, 'qa/.auth/storage.json');
const OUT = path.join(root, 'qa/baseline-private');
fs.mkdirSync(OUT, { recursive: true });

const PAGES = [
	{ id: 'dashboard', url: '/my-account/' },
	{ id: 'orders', url: '/my-account/orders/' },
	{ id: 'addresses', url: '/my-account/edit-address/' },
	{ id: 'edit-account', url: '/my-account/edit-account/' },
	{ id: 'downloads', url: '/my-account/downloads/' },
	{ id: 'wishlist', url: '/%d8%b9%d9%84%d8%a7%d9%82%d9%87-%d9%85%d9%86%d8%af%db%8c-%d9%87%d8%a7/' },
];

const browser = await chromium.launch({ headless: true });
const opts = {
	viewport: { width: 1440, height: 900 },
	locale: 'fa-IR',
	reducedMotion: 'reduce',
};
if (fs.existsSync(AUTH)) opts.storageState = AUTH;
else {
	console.warn('No qa/.auth/storage.json — open login.mjs first for full private set.');
}

const ctx = await browser.newContext(opts);
const page = await ctx.newPage();
const saved = [];
for (const p of PAGES) {
	try {
		await page.goto(`${SITE}${p.url}`, { waitUntil: 'domcontentloaded', timeout: 60000 });
		await preparePage(page);
		const loggedIn = await page.evaluate(() => !document.body.innerText.includes('ورود | ثبت‌نام') && !document.querySelector('form.woocommerce-form-login'));
		const dir = path.join(OUT, p.id);
		await shotFull(page, path.join(dir, '1440.png'), 1440);
		saved.push({ id: p.id, loggedIn, file: `qa/baseline-private/${p.id}/1440.png` });
		console.log(p.id, loggedIn ? 'ok' : 'maybe-guest');
	} catch (e) {
		saved.push({ id: p.id, error: e.message });
	}
}
fs.writeFileSync(path.join(OUT, 'manifest.json'), JSON.stringify({ savedAt: new Date().toISOString(), saved }, null, 2));
await browser.close();
console.log('private capture done', saved.length);
