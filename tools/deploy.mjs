/**
 * Secure FTP deploy via curl (no DELETE). Secrets from .env.local only.
 * Usage: node tools/deploy.mjs [--dry-run] [--what theme|preview|all]
 */
import fs from 'fs';
import path from 'path';
import { spawnSync } from 'child_process';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
dotenv.config({ path: path.join(root, '.env.local') });

const dryRun = process.argv.includes('--dry-run');
const whatIdx = process.argv.indexOf('--what');
const what = whatIdx >= 0 ? process.argv[whatIdx + 1] : 'all';

const host = (process.env.FTP_HOST || '').replace(/^ftp:\/\//i, '');
const user = process.env.FTP_USER || '';
const pass = process.env.FTP_PASS || '';
const ftpRoot = (process.env.FTP_ROOT || '').replace(/\\/g, '/').replace(/\/$/, '');
const previewKey = process.env.BAGLLET_PREVIEW_KEY || '';

if (!host || !user || !pass) {
	console.error('FTP_HOST/USER/PASS required');
	process.exit(1);
}

function assertAllowed(remoteRel) {
	const norm = remoteRel.replace(/\\/g, '/').replace(/^\//, '');
	const ok =
		/^wp-content\/themes\/bagllet(\/|$)/.test(norm) ||
		/^wp-content\/plugins\/bagllet-widgets(\/|$)/.test(norm) ||
		/^wp-content\/mu-plugins\/bagllet-[^/]+$/.test(norm);
	if (!ok) throw new Error(`Blocked remote path: ${norm}`);
}

function remoteUrl(remoteRel) {
	const norm = remoteRel.replace(/\\/g, '/').replace(/^\//, '');
	assertAllowed(norm);
	const full = ftpRoot ? `${ftpRoot}/${norm}` : `/${norm}`;
	return `ftp://${host}${full.startsWith('/') ? full : `/${full}`}`;
}

function curlUpload(localPath, remoteRel) {
	assertAllowed(remoteRel);
	const url = remoteUrl(remoteRel);
	console.log(`${dryRun ? 'DRY' : 'PUT'} ${path.relative(root, localPath)} -> ${remoteRel}`);
	if (dryRun) return;
	const stamp = new Date().toISOString().replace(/[:.]/g, '-');
	const bakDir = path.join(root, 'qa/backups');
	fs.mkdirSync(bakDir, { recursive: true });
	const bak = path.join(bakDir, `${remoteRel.replace(/[\\/]/g, '_')}.${stamp}.bak`);
	// best-effort backup
	spawnSync(
		'curl.exe',
		['-sS', '--ftp-pasv', '-u', `${user}:${pass}`, url, '-o', bak],
		{ encoding: 'utf8' }
	);
	const r = spawnSync(
		'curl.exe',
		['-sS', '--ftp-pasv', '--ftp-create-dirs', '-u', `${user}:${pass}`, '-T', localPath, url],
		{ encoding: 'utf8' }
	);
	if (r.status !== 0) {
		throw new Error(r.stderr || r.stdout || `curl failed ${r.status}`);
	}
}

function walk(dir) {
	const out = [];
	for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
		const p = path.join(dir, e.name);
		if (e.isDirectory()) out.push(...walk(p));
		else out.push(p);
	}
	return out;
}

function publishTree(localDir, remotePrefix) {
	const base = path.resolve(localDir);
	for (const file of walk(base)) {
		const rel = path.relative(base, file).replace(/\\/g, '/');
		curlUpload(file, `${remotePrefix}/${rel}`);
	}
}

// write preview key locally (gitignored)
const keyLocal = path.join(root, 'public_html/wp-content/mu-plugins/bagllet-preview-key.php');
if (previewKey) {
	const content = `<?php\nif ( ! defined( 'ABSPATH' ) ) { exit; }\ndefine( 'BAGLLET_PREVIEW_KEY', '${previewKey.replace(/\\/g, '\\\\').replace(/'/g, "\\'")}');\n`;
	if (!dryRun) fs.writeFileSync(keyLocal, content, 'utf8');
	console.log('preview key file ready (local)');
}

if (what === 'theme' || what === 'all') {
	publishTree(path.join(root, 'public_html/wp-content/themes/bagllet'), 'wp-content/themes/bagllet');
}
if (what === 'preview' || what === 'all') {
	curlUpload(
		path.join(root, 'public_html/wp-content/mu-plugins/bagllet-preview.php'),
		'wp-content/mu-plugins/bagllet-preview.php'
	);
	if (fs.existsSync(keyLocal)) {
		curlUpload(keyLocal, 'wp-content/mu-plugins/bagllet-preview-key.php');
	}
}

console.log('Deploy finished.');
