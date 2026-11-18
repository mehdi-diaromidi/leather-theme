/**
 * Pixel diff helper: compare a live/captured PNG against qa/baseline.
 * Usage:
 *   node qa/diff/compare.mjs --page home --width 1440 --candidate path/to.png
 *   node qa/diff/compare.mjs --self-test
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

function parseArgs(argv) {
	const out = {};
	for (let i = 0; i < argv.length; i++) {
		const a = argv[i];
		if (a === '--self-test') out.selfTest = true;
		else if (a.startsWith('--')) {
			out[a.slice(2)] = argv[i + 1];
			i++;
		}
	}
	return out;
}

function readPng(file) {
	const buf = fs.readFileSync(file);
	return PNG.sync.read(buf);
}

function diffFiles(baselinePath, candidatePath, outPath) {
	const img1 = readPng(baselinePath);
	const img2 = readPng(candidatePath);
	const width = Math.min(img1.width, img2.width);
	const height = Math.min(img1.height, img2.height);
	if (img1.width !== img2.width || img1.height !== img2.height) {
		console.warn(`size mismatch baseline=${img1.width}x${img1.height} candidate=${img2.width}x${img2.height}; cropping to ${width}x${height}`);
	}
	const diff = new PNG({ width, height });
	const crop = (img) => {
		const out = new PNG({ width, height });
		for (let y = 0; y < height; y++) {
			for (let x = 0; x < width; x++) {
				const si = (y * img.width + x) << 2;
				const di = (y * width + x) << 2;
				out.data[di] = img.data[si];
				out.data[di + 1] = img.data[si + 1];
				out.data[di + 2] = img.data[si + 2];
				out.data[di + 3] = img.data[si + 3];
			}
		}
		return out;
	};
	const a = crop(img1);
	const b = crop(img2);
	const mismatched = pixelmatch(a.data, b.data, diff.data, width, height, { threshold: 0.1 });
	fs.writeFileSync(outPath, PNG.sync.write(diff));
	const total = width * height;
	return {
		width,
		height,
		mismatched,
		total,
		percent: total ? +((mismatched / total) * 100).toFixed(4) : 0,
		diffPath: outPath,
	};
}

const args = parseArgs(process.argv.slice(2));

if (args.selfTest) {
	const sample = path.join(root, 'qa/baseline/home/1440.png');
	if (!fs.existsSync(sample)) {
		console.error('missing baseline sample', sample);
		process.exit(1);
	}
	const result = diffFiles(sample, sample, path.join(reportDir, 'diff-self-home-1440.png'));
	fs.writeFileSync(path.join(reportDir, 'diff-self-test.json'), JSON.stringify(result, null, 2));
	console.log('self-test', result);
	process.exit(result.mismatched === 0 ? 0 : 1);
}

const page = args.page;
const width = args.width || '1440';
const candidate = args.candidate;
if (!page || !candidate) {
	console.error('Usage: node qa/diff/compare.mjs --page <id> --width 1440 --candidate <png> | --self-test');
	process.exit(2);
}
const baseline = path.join(root, 'qa/baseline', page, `${width}.png`);
if (!fs.existsSync(baseline)) {
	console.error('baseline missing', baseline);
	process.exit(1);
}
if (!fs.existsSync(candidate)) {
	console.error('candidate missing', candidate);
	process.exit(1);
}
const result = diffFiles(
	baseline,
	candidate,
	path.join(reportDir, `diff-${page}-${width}.png`)
);
result.page = page;
result.widthLabel = width;
result.baseline = baseline;
result.candidate = candidate;
fs.writeFileSync(path.join(reportDir, `diff-${page}-${width}.json`), JSON.stringify(result, null, 2));
console.log(JSON.stringify(result, null, 2));
