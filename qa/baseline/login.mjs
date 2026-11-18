/**
 * Save authenticated storage for private baselines.
 * Opens a headed Chromium window; log in manually, then press Enter in the terminal.
 * Storage: qa/.auth/storage.json (gitignored)
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import readline from 'readline';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
dotenv.config({ path: path.join(root, '.env.local') });
const SITE = (process.env.SITE_URL || 'https://bagllet.ir').replace(/\/$/, '');
const AUTH = path.join(root, 'qa/.auth');
fs.mkdirSync(AUTH, { recursive: true });

const browser = await chromium.launch({ headless: false });
const context = await browser.newContext({ locale: 'fa-IR' });
const page = await context.newPage();
await page.goto(`${SITE}/my-account/`, { waitUntil: 'domcontentloaded' });
console.log('Log in in the opened browser window, then return here and press Enter…');

await new Promise((resolve) => {
	const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
	rl.question('Press Enter after login… ', () => {
		rl.close();
		resolve();
	});
});

const out = path.join(AUTH, 'storage.json');
await context.storageState({ path: out });
console.log('Saved', out);
await browser.close();
