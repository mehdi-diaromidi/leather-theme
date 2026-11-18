/**
 * Ensure theme.json palette colors match tokens.css hex values.
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const tokensPath = path.join(root, 'public_html/wp-content/themes/bagllet/assets/css/tokens.css');
const themeJsonPath = path.join(root, 'public_html/wp-content/themes/bagllet/theme.json');

function normalizeHex(h) {
	let x = h.toLowerCase();
	if (/^#[0-9a-f]{3}$/.test(x)) {
		x = `#${x[1]}${x[1]}${x[2]}${x[2]}${x[3]}${x[3]}`;
	}
	return x;
}

const tokens = fs.readFileSync(tokensPath, 'utf8');
const cssVars = {};
for (const m of tokens.matchAll(/--([a-z0-9-]+)\s*:\s*(#[0-9a-fA-F]{3,8})\b/g)) {
	cssVars[m[1]] = normalizeHex(m[2]);
}

const theme = JSON.parse(fs.readFileSync(themeJsonPath, 'utf8'));
const palette = theme.settings?.color?.palette || [];
const issues = [];
const map = {
	black: 'color-black',
	white: 'color-white',
	clay: 'color-clay',
	sand: 'color-sand',
	ivory: 'color-ivory',
	bg: 'color-bg',
	'gray-100': 'color-gray-100',
	border: 'color-border',
	'text-muted': 'color-text-muted',
	error: 'color-error',
	success: 'color-success',
};

for (const item of palette) {
	const key = map[item.slug];
	if (!key) {
		issues.push({ slug: item.slug, problem: 'no-css-mapping' });
		continue;
	}
	const expected = cssVars[key];
	const got = normalizeHex(item.color);
	if (!expected) {
		issues.push({ slug: item.slug, problem: 'missing-css-var', key });
	} else if (expected !== got) {
		issues.push({ slug: item.slug, problem: 'mismatch', expected, got });
	}
}

const reportDir = path.join(root, 'qa/reports/phase-2');
fs.mkdirSync(reportDir, { recursive: true });
const report = { ok: issues.length === 0, issues, checked: palette.length };
fs.writeFileSync(path.join(reportDir, 'tokens-sync.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
process.exit(issues.length ? 1 : 0);
