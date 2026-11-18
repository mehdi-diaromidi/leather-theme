/**
 * Forbidden pattern checks for the bagllet theme (+ advisory scan of bagllet-widgets).
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const theme = path.join(root, 'public_html/wp-content/themes/bagllet');
const mu = path.join(root, 'public_html/wp-content/mu-plugins');
const widgets = path.join(root, 'public_html/wp-content/plugins/bagllet-widgets');

const issues = [];
const advisory = [];

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

function scanWoodmart(files, bucket, blocking) {
	for (const file of files) {
		const rel = path.relative(root, file).replace(/\\/g, '/');
		const text = fs.readFileSync(file, 'utf8');
		text.split(/\r?\n/).forEach((line, idx) => {
			if (!/wd-|xts-|woodmart/i.test(line)) return;
			if (/بدون Woodmart|بدون وابستگی به Woodmart|not.*Woodmart|Woodmart.*forbidden|legacy.*Woodmart|dequeue|parity/i.test(line)) {
				return;
			}
			if (/bagllet-preview\.php/.test(rel) && /Woodmart|bagllet-widgets/.test(line)) return;
			bucket.push({ file: rel, line: idx + 1, rule: 'woodmart-class', sample: line.trim().slice(0, 120), blocking });
		});
	}
}

const codeFiles = walk(theme, (p) => /\.(php|css|js|json)$/i.test(p));
const muFiles = walk(mu, (p) => /\.php$/i.test(p) && !p.endsWith('.example.php') && !p.endsWith('bagllet-preview-key.php'));
scanWoodmart([...codeFiles, ...muFiles], issues, true);

const widgetFiles = walk(widgets, (p) => /\.(php|css|js)$/i.test(p));
scanWoodmart(widgetFiles, advisory, false);

const cssFiles = walk(path.join(theme, 'assets/css'), (p) => p.endsWith('.css'));
const tokenFile = path.join(theme, 'assets/css/tokens.css');
const tokenText = fs.readFileSync(tokenFile, 'utf8');
const allowedHex = new Set([...tokenText.matchAll(/#[0-9a-fA-F]{3,8}\b/g)].map((m) => m[0].toLowerCase()));
const allowedRgb = new Set([...tokenText.matchAll(/rgba?\([^)]+\)/gi)].map((m) => m[0].toLowerCase().replace(/\s+/g, '')));

for (const file of cssFiles) {
	const rel = path.relative(root, file).replace(/\\/g, '/');
	if (rel.endsWith('tokens.css')) continue;
	const text = fs.readFileSync(file, 'utf8');
	text.split(/\r?\n/).forEach((line, idx) => {
		for (const m of line.matchAll(/#[0-9a-fA-F]{3,8}\b/g)) {
			if (!allowedHex.has(m[0].toLowerCase())) {
				issues.push({ file: rel, line: idx + 1, rule: 'hex-outside-tokens', sample: line.trim().slice(0, 120) });
			}
		}
		for (const m of line.matchAll(/rgba?\([^)]+\)/gi)) {
			const n = m[0].toLowerCase().replace(/\s+/g, '');
			if (!allowedRgb.has(n) && !/var\(/.test(line)) {
				issues.push({ file: rel, line: idx + 1, rule: 'rgb-outside-tokens', sample: line.trim().slice(0, 120) });
			}
		}
		// px outside var()/calc — skip @media breakpoint lines (values documented as --bp-* in tokens.css)
		if (
			/:\s*[^;]*\d+px/.test(line) &&
			!/@media/.test(line) &&
			!/var\(|calc\(|minmax\(|clamp\(|url\(/.test(line) &&
			!/^\s*\/\*/.test(line)
		) {
			issues.push({ file: rel, line: idx + 1, rule: 'px-literal-outside-tokens', sample: line.trim().slice(0, 120) });
		}
		if (/!important/.test(line) && !/\/\*.*!important|a11y|reduced motion/i.test(line)) {
			issues.push({ file: rel, line: idx + 1, rule: 'important-without-comment', sample: line.trim().slice(0, 120) });
		}
	});
}

const reportDir = path.join(root, 'qa/reports/phase-2');
fs.mkdirSync(reportDir, { recursive: true });
const report = {
	ok: issues.length === 0,
	count: issues.length,
	issues,
	advisoryWoodmartInWidgets: advisory.length,
	advisorySample: advisory.slice(0, 20),
};
fs.writeFileSync(path.join(reportDir, 'forbidden.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
process.exit(issues.length ? 1 : 0);
