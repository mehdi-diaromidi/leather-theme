/**
 * Pixel + optional specs diff against qa/baseline.
 *
 * node qa/diff/compare.mjs --page home --width 1440 --candidate path.png
 * node qa/diff/compare.mjs --all --candidate-dir qa/candidates
 * node qa/diff/compare.mjs --noise-floor  (compare two woodmart captures if present)
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const reportDir = path.join(root, 'qa/reports/phase-2');
fs.mkdirSync(reportDir, { recursive: true });
const THRESHOLD_PCT = 0.3;

function parseArgs(argv) {
	const out = { _: [] };
	for (let i = 0; i < argv.length; i++) {
		const a = argv[i];
		if (a === '--all') out.all = true;
		else if (a === '--noise-floor') out.noiseFloor = true;
		else if (a.startsWith('--')) {
			out[a.slice(2)] = argv[i + 1];
			i++;
		} else out._.push(a);
	}
	return out;
}

function readPng(file) {
	return PNG.sync.read(fs.readFileSync(file));
}

function diffFiles(baselinePath, candidatePath, outPath) {
	const img1 = readPng(baselinePath);
	const img2 = readPng(candidatePath);
	if (img1.width !== img2.width || img1.height !== img2.height) {
		return {
			ok: false,
			reason: 'size-mismatch',
			baseline: { w: img1.width, h: img1.height },
			candidate: { w: img2.width, h: img2.height },
			percent: null,
			mismatched: null,
		};
	}
	const { width, height } = img1;
	const diff = new PNG({ width, height });
	const mismatched = pixelmatch(img1.data, img2.data, diff.data, width, height, { threshold: 0.1 });
	fs.writeFileSync(outPath, PNG.sync.write(diff));
	const total = width * height;
	const percent = total ? +((mismatched / total) * 100).toFixed(4) : 0;
	return {
		ok: percent <= THRESHOLD_PCT,
		width,
		height,
		mismatched,
		total,
		percent,
		thresholdPct: THRESHOLD_PCT,
		diffPath: outPath,
	};
}

const args = parseArgs(process.argv.slice(2));
const rows = [];

if (args.noiseFloor) {
	const a = path.join(root, 'qa/candidates/noise-a/home/1440.png');
	const b = path.join(root, 'qa/candidates/noise-b/home/1440.png');
	if (!fs.existsSync(a) || !fs.existsSync(b)) {
		console.error('noise-floor needs qa/candidates/noise-a|b/home/1440.png');
		process.exit(1);
	}
	const result = diffFiles(a, b, path.join(reportDir, 'diff-noise-floor-home-1440.png'));
	fs.writeFileSync(path.join(reportDir, 'noise-floor.json'), JSON.stringify(result, null, 2));
	console.log(JSON.stringify(result, null, 2));
	process.exit(result.ok === false && result.reason === 'size-mismatch' ? 1 : 0);
}

function one(page, width, candidate) {
	const baseline = path.join(root, 'qa/baseline', page, `${width}.png`);
	if (!fs.existsSync(baseline)) return { page, width, ok: false, reason: 'baseline-missing' };
	if (!fs.existsSync(candidate)) return { page, width, ok: false, reason: 'candidate-missing' };
	const result = diffFiles(baseline, candidate, path.join(reportDir, `diff-${page}-${width}.png`));
	return { page, width, candidate, baseline, ...result };
}

if (args.all) {
	const dir = args['candidate-dir'] || path.join(root, 'qa/candidates/preview');
	const pages = fs
		.readdirSync(path.join(root, 'qa/baseline'), { withFileTypes: true })
		.filter((d) => d.isDirectory() && d.name !== 'specs' && d.name !== 'states')
		.map((d) => d.name);
	for (const page of pages) {
		for (const width of [1440, 768, 390]) {
			const candidate = path.join(dir, page, `${width}.png`);
			if (!fs.existsSync(candidate)) continue;
			rows.push(one(page, width, candidate));
		}
	}
} else {
	if (!args.page || !args.candidate) {
		console.error('Usage: --page --width --candidate | --all --candidate-dir | --noise-floor');
		process.exit(2);
	}
	rows.push(one(args.page, args.width || '1440', args.candidate));
}

const summary = {
	thresholdPct: THRESHOLD_PCT,
	pass: rows.filter((r) => r.ok).length,
	fail: rows.filter((r) => !r.ok).length,
	rows,
};
fs.writeFileSync(path.join(reportDir, 'diff-summary.json'), JSON.stringify(summary, null, 2));
const md = [
	'# Diff summary',
	'',
	`Threshold: ${THRESHOLD_PCT}% · size mismatch = fail`,
	'',
	'| page | width | % | result |',
	'|---|---:|---:|---|',
	...rows.map((r) => `| ${r.page} | ${r.width} | ${r.percent ?? r.reason} | ${r.ok ? 'PASS' : 'FAIL'} |`),
	'',
].join('\n');
fs.writeFileSync(path.join(reportDir, 'diff-summary.md'), md);
console.log(md);
process.exit(summary.fail ? 1 : 0);
