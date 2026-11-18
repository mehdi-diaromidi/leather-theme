/**
 * Capture candidate screenshots (preview or noise-floor woodmart).
 * Usage:
 *   node qa/diff/capture-candidate.mjs --mode preview --out qa/candidates/preview
 *   node qa/diff/capture-candidate.mjs --mode woodmart --out qa/candidates/noise-a
 *   node qa/diff/capture-candidate.mjs --mode woodmart --out qa/candidates/noise-b
 *   node qa/diff/capture-candidate.mjs --mode preview --specs
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { preparePage, shotFull, collectSpecs, KEY_SELECTORS, STYLE_PROPS } from '../baseline/lib.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });

const SITE = (process.env.SITE_URL || 'https://baglletleather.com').replace(/\/$/, '');
const key = process.env.BAGLLET_PREVIEW_KEY || '';
const pagesConfig = JSON.parse(fs.readFileSync(path.join(root, 'qa/baseline/pages.json'), 'utf8'));
const WIDTHS = pagesConfig.widths || [1440, 768, 390];

function arg(name, fallback) {
	const i = process.argv.indexOf(`--${name}`);
	return i >= 0 ? process.argv[i + 1] : fallback;
}

const mode = arg('mode', 'preview');
const outRoot = path.resolve(root, arg('out', mode === 'preview' ? 'qa/candidates/preview' : 'qa/candidates/noise-a'));
const doSpecs = process.argv.includes('--specs');
const pageFilter = arg('pages', 'home,shop,about');

if (mode === 'preview' && !key) {
	console.error('BAGLLET_PREVIEW_KEY required for preview mode');
	process.exit(1);
}

const pages = pagesConfig.pages.filter((p) => pageFilter.split(',').includes(p.id));
fs.mkdirSync(outRoot, { recursive: true });

const resolveIp = process.env.BAGLLET_RESOLVE_IP || '';
// System HTTP proxy often breaks Playwright to the staging host; prefer direct + optional MAP.
const launchArgs = ['--no-proxy-server', '--proxy-server=direct://'];
if (resolveIp) {
	launchArgs.push(
		`--host-resolver-rules=MAP baglletleather.com ${resolveIp}, MAP www.baglletleather.com ${resolveIp}, MAP bagllet.ir ${resolveIp}, MAP www.bagllet.ir ${resolveIp}`
	);
}
const browser = await chromium.launch({
	headless: true,
	args: launchArgs,
});
const report = [];

for (const entry of pages) {
	for (const width of WIDTHS) {
		const ctx = await browser.newContext({
			viewport: { width, height: 900 },
			locale: 'fa-IR',
			reducedMotion: 'reduce',
			ignoreHTTPSErrors: !!resolveIp,
		});
		const page = await ctx.newPage();
		let url = entry.url.startsWith('http') ? entry.url : `${SITE}${entry.url}`;
		if (mode === 'preview') {
			const u = new URL(url);
			u.searchParams.set('bagllet_preview', key);
			url = u.toString();
		}
		try {
			await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 90000 });
			await preparePage(page, { maskSelectors: pagesConfig.maskSelectors || [] });
			const dir = path.join(outRoot, entry.id);
			fs.mkdirSync(dir, { recursive: true });
			await shotFull(page, path.join(dir, `${width}.png`), width);
			const theme = await page.evaluate(() => {
				const links = [...document.querySelectorAll('link[rel=stylesheet], script[src]')].map((el) => el.href || el.src);
				return {
					bodyClass: document.body.className,
					hasBagllet: links.some((u) => /\/themes\/bagllet\//.test(u)),
					hasWoodmart: links.some((u) => /\/themes\/woodmart\//.test(u)),
					sampleLinks: links.filter((u) => /\/themes\//.test(u)).slice(0, 8),
				};
			});
			if (doSpecs) {
				const specs = await collectSpecs(page, entry.id, width);
				fs.writeFileSync(path.join(dir, `specs-${width}.json`), JSON.stringify(specs, null, 2));
			}
			report.push({ id: entry.id, width, mode, ...theme });
			console.log(entry.id, width, theme.hasBagllet ? 'bagllet' : '', theme.hasWoodmart ? 'woodmart' : '');
		} catch (e) {
			report.push({ id: entry.id, width, error: e.message });
			console.error(entry.id, width, e.message);
		}
		await ctx.close();
	}
}

await browser.close();
fs.writeFileSync(path.join(outRoot, 'capture-report.json'), JSON.stringify(report, null, 2));
console.log('done', outRoot);
