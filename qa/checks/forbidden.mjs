/**
 * Forbidden pattern checks for the bagllet theme.
 * Usage: node qa/checks/forbidden.mjs
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const theme = path.join(root, 'public_html/wp-content/themes/bagllet');
const mu = path.join(root, 'public_html/wp-content/mu-plugins');

const issues = [];

function walk(dir, filter) {
	if (!fs.existsSync(dir)) return [];
	const out = [];
	for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
		const p = path.join(dir, e.name);
		if (e.isDirectory()) out.push(...walk(p, filter));
		else if (filter(p)) out.push(p);
	}
	return out;
}

const codeFiles = walk(theme, (p) => /\.(php|css|js|json)$/i.test(p));
const muFiles = walk(mu, (p) => /\.php$/i.test(p) && !p.endsWith('.example.php'));

for (const file of [...codeFiles, ...muFiles]) {
	const rel = path.relative(root, file).replace(/\\/g, '/');
	const text = fs.readFileSync(file, 'utf8');
	const lines = text.split(/\r?\n/);
	lines.forEach((line, idx) => {
		if (/wd-|xts-|woodmart/i.test(line) && !/woodmart.*انداز|measure|بدون Woodmart|بدون وابستگی به Woodmart/i.test(line)) {
			// Allow documentation strings that explicitly forbid Woodmart.
			if (/بدون Woodmart|بدون وابستگی به Woodmart|not.*Woodmart|Woodmart.*forbidden|legacy.*Woodmart/i.test(line)) {
				return;
			}
			if (/bagllet-preview\.php/.test(rel) && /Woodmart|bagllet-widgets/.test(line)) {
				// preview mu-plugin mentions Woodmart only to dequeue legacy assets.
				return;
			}
			issues.push({ file: rel, line: idx + 1, rule: 'woodmart-class', sample: line.trim().slice(0, 120) });
		}
	});
}

const cssFiles = walk(path.join(theme, 'assets/css'), (p) => p.endsWith('.css'));
const tokenFile = path.join(theme, 'assets/css/tokens.css');
const tokenText = fs.readFileSync(tokenFile, 'utf8');
const allowedHex = new Set(
	[...tokenText.matchAll(/#[0-9a-fA-F]{3,8}\b/g)].map((m) => m[0].toLowerCase())
);

for (const file of cssFiles) {
	const rel = path.relative(root, file).replace(/\\/g, '/');
	if (rel.endsWith('tokens.css')) continue;
	const text = fs.readFileSync(file, 'utf8');
	const lines = text.split(/\r?\n/);
	lines.forEach((line, idx) => {
		for (const m of line.matchAll(/#[0-9a-fA-F]{3,8}\b/g)) {
			if (!allowedHex.has(m[0].toLowerCase())) {
				issues.push({ file: rel, line: idx + 1, rule: 'hex-outside-tokens', sample: line.trim().slice(0, 120) });
			}
		}
		if (/!important/.test(line) && !/\/\*.*!important|a11y|reduced motion/i.test(line)) {
			issues.push({ file: rel, line: idx + 1, rule: 'important-without-comment', sample: line.trim().slice(0, 120) });
		}
	});
}

const reportDir = path.join(root, 'qa/reports/phase-2');
fs.mkdirSync(reportDir, { recursive: true });
const report = { ok: issues.length === 0, count: issues.length, issues };
fs.writeFileSync(path.join(reportDir, 'forbidden.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
process.exit(issues.length ? 1 : 0);
