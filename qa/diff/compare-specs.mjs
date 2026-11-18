/**
 * Compare candidate specs JSON to baseline specs (±1px on numeric lengths).
 * node qa/diff/compare-specs.mjs --candidate-dir qa/candidates/preview
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const i = process.argv.indexOf('--candidate-dir');
const candDir = path.resolve(root, i >= 0 ? process.argv[i + 1] : 'qa/candidates/preview');
const TOL = 1;

function parsePx(v) {
	if (typeof v !== 'string') return null;
	const m = v.trim().match(/^(-?[\d.]+)px$/);
	return m ? Number(m[1]) : null;
}

const rows = [];
for (const page of fs.readdirSync(candDir, { withFileTypes: true }).filter((d) => d.isDirectory())) {
	for (const width of [1440, 768, 390]) {
		const cand = path.join(candDir, page.name, `specs-${width}.json`);
		const base = path.join(root, 'qa/baseline/specs', `${page.name}-${width}.json`);
		if (!fs.existsSync(cand) || !fs.existsSync(base)) continue;
		const a = JSON.parse(fs.readFileSync(base, 'utf8'));
		const b = JSON.parse(fs.readFileSync(cand, 'utf8'));
		const diffs = [];
		for (const [name, el] of Object.entries(a.elements || {})) {
			const other = b.elements?.[name];
			if (!el?.style || !other?.style) continue;
			for (const prop of ['fontSize', 'paddingTop', 'paddingRight', 'width', 'height', 'borderRadius']) {
				const x = parsePx(el.style[prop]);
				const y = parsePx(other.style[prop]);
				if (x === null || y === null) continue;
				if (Math.abs(x - y) > TOL) diffs.push({ name, prop, base: x, cand: y });
			}
		}
		rows.push({ page: page.name, width, diffs: diffs.length, sample: diffs.slice(0, 8) });
	}
}

const out = path.join(root, 'qa/reports/phase-2/specs-diff.json');
fs.mkdirSync(path.dirname(out), { recursive: true });
fs.writeFileSync(out, JSON.stringify({ tolerancePx: TOL, rows }, null, 2));
console.log(JSON.stringify({ tolerancePx: TOL, pages: rows.length, rows }, null, 2));
