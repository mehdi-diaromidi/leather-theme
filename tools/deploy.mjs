/**
 * Secure FTP deploy via curl (no DELETE). Secrets from .env.local only.
 * Usage: node tools/deploy.mjs [--dry-run] [--what theme|preview|key|all]
 * Order for live switch: theme → key → preview (last).
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
	let norm = remoteRel.replace(/\\/g, '/');
	norm = path.posix.normalize(norm).replace(/^\//, '');
	if (norm.includes('..')) throw new Error(`Blocked path traversal: ${remoteRel}`);
	const ok =
		/^wp-content\/themes\/bagllet(\/|$)/.test(norm) ||
		/^wp-content\/plugins\/bagllet-widgets(\/|$)/.test(norm) ||
		/^wp-content\/mu-plugins\/bagllet-[^/]+$/.test(norm);
	if (!ok) throw new Error(`Blocked remote path: ${norm}`);
	return norm;
}

function remoteUrl(remoteRel) {
	const norm = assertAllowed(remoteRel);
	const full = ftpRoot ? `${ftpRoot}/${norm}` : `/${norm}`;
	return `ftp://${host}${full.startsWith('/') ? full : `/${full}`}`;
}

function curlConfigAuth() {
	// Avoid putting credentials on argv (visible in process list).
	return `user = "${user}:${pass.replace(/\\/g, '\\\\').replace(/"/g, '\\"')}"\n`;
}

function curlRun(args, { allowFail = false } = {}) {
	const conf = curlConfigAuth();
	const r = spawnSync('curl.exe', ['-sS', '--config', '-', ...args], {
		input: conf,
		encoding: 'utf8',
	});
	if (!allowFail && r.status !== 0) {
		const err = (r.stderr || r.stdout || '').trim();
		if (/530/.test(err)) {
			throw new Error('FTP 530 — stop retrying to avoid account lock. Report to manager.');
		}
		throw new Error(err || `curl failed ${r.status}`);
	}
	return r;
}

function curlUpload(localPath, remoteRel) {
	const norm = assertAllowed(remoteRel);
	const url = remoteUrl(norm);
	console.log(`${dryRun ? 'DRY' : 'PUT'} ${path.relative(root, localPath)} -> ${norm}`);
	if (dryRun) return;

	const stamp = new Date().toISOString().replace(/[:.]/g, '-');
	const bakDir = path.join(root, 'qa/backups');
	fs.mkdirSync(bakDir, { recursive: true });
	const bak = path.join(bakDir, `${norm.replace(/[\\/]/g, '_')}.${stamp}.bak`);
	curlRun(['--ftp-pasv', url, '-o', bak], { allowFail: true });

	let r = curlRun(['--ftp-pasv', '--ftp-create-dirs', '-T', localPath, url], { allowFail: true });
	if (r.status !== 0) {
		// Retry with SSL if server supports AUTH TLS.
		console.warn('plain FTP failed; retrying with --ssl-reqd');
		r = curlRun(['--ssl-reqd', '--ftp-pasv', '--ftp-create-dirs', '-T', localPath, url], { allowFail: true });
		if (r.status !== 0) {
			const err = (r.stderr || r.stdout || '').trim();
			if (/530/.test(err)) throw new Error('FTP 530 — stop retrying to avoid account lock. Report to manager.');
			console.warn('TLS also failed; server may be plain FTP only:', err.slice(0, 160));
			throw new Error(err || 'upload failed');
		}
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

const keyLocal = path.join(root, 'public_html/wp-content/mu-plugins/bagllet-preview-key.php');
if (previewKey) {
	const content = `<?php\nif ( ! defined( 'ABSPATH' ) ) { exit; }\ndefine( 'BAGLLET_PREVIEW_KEY', '${previewKey.replace(/\\/g, '\\\\').replace(/'/g, "\\'")}');\n`;
	if (!dryRun) fs.writeFileSync(keyLocal, content, 'utf8');
	console.log('preview key file ready (local)');
}

if (what === 'theme' || what === 'all') {
	publishTree(path.join(root, 'public_html/wp-content/themes/bagllet'), 'wp-content/themes/bagllet');
}
if (what === 'key' || what === 'all' || what === 'preview') {
	if (fs.existsSync(keyLocal)) {
		curlUpload(keyLocal, 'wp-content/mu-plugins/bagllet-preview-key.php');
	}
}
if (what === 'preview' || what === 'all') {
	curlUpload(
		path.join(root, 'public_html/wp-content/mu-plugins/bagllet-preview.php'),
		'wp-content/mu-plugins/bagllet-preview.php'
	);
}

console.log('Deploy finished.');
console.log('Rollback: delete wp-content/mu-plugins/bagllet-preview.php and bagllet-preview-key.php via host FTP/panel.');
