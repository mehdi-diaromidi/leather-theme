import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const out = [];

function pngSize(file) {
	const buf = fs.readFileSync(file);
	if (buf.toString('ascii', 1, 4) !== 'PNG') return null;
	return { width: buf.readUInt32BE(16), height: buf.readUInt32BE(20) };
}

function walk(dir, base = '') {
	for (const name of fs.readdirSync(dir)) {
		const p = path.join(dir, name);
		const rel = path.join(base, name).replace(/\\/g, '/');
		if (fs.statSync(p).isDirectory()) {
			if (name === 'specs') continue;
			walk(p, rel);
		} else if (name.endsWith('.png')) {
			const s = pngSize(p);
			out.push({ file: rel, ...s });
		}
	}
}

walk(__dirname);
const reportDir = path.join(root, 'qa/reports/phase-1');
fs.mkdirSync(reportDir, { recursive: true });
fs.writeFileSync(path.join(reportDir, 'png-dimensions.json'), JSON.stringify(out, null, 2));
const bad = out.filter((x) => {
	const m = x.file.match(/(^|\/)(1440|768|390|1280|1024)\.png$/);
	if (!m) return false;
	return x.width !== Number(m[2]);
});
console.log('pngs', out.length, 'widthMismatches', bad.length);
if (bad.length) console.log(bad.slice(0, 20));
