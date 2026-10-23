#!/usr/bin/env node
/** Package the runtime-only child theme ZIP. */
import { execFileSync } from 'node:child_process';
import { cpSync, mkdirSync, mkdtempSync, readFileSync, rmSync, statSync } from 'node:fs';
import { tmpdir } from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { globSync } from 'glob';

const ROOT = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '..' );
const versionSource = readFileSync( path.join( ROOT, 'inc/bootstrap/version.php' ), 'utf8' );
const match = versionSource.match( /define\(\s*['"]NEXTSAAS_VERSION['"]\s*,\s*['"]([^'"]+)['"]\s*\)/ );
if ( ! match ) throw new Error( 'NEXTSAAS_VERSION was not found in inc/bootstrap/version.php' );

const include = [
	// `blocks/blocks.php` is the registrar that loads every block manifest; the
	// per-block globs below only cover the blocks themselves. A clean install fails
	// with a fatal error in functions.php without it, which is exactly what the
	// packaged-install check is for.
	'functions.php', 'style.css', 'theme.json', 'inc/**', 'templates/**', 'parts/**', 'patterns/**',
	'blocks/blocks.php',
	'blocks/*/block.json', 'blocks/*/render.php', 'blocks/*/style.css', 'blocks/*/editor.css',
	'blocks/**/*.js', 'blocks/**/*.asset.php', 'blocks/**/class-*.php', 'blocks/**/register-editor.php',
	'assets/**', 'languages/**', 'README.md', 'CHANGELOG.md', 'LICENSE*',
];
const ignore = [
	'assets/**/*.map', 'resources/**', 'scripts/**', 'tests/**', 'node_modules/**', 'vendor/**', '.git/**',
	'**/*.map', '.phpunit.cache/**', '.php-cs-fixer.cache', 'composer.*', 'package*.json', 'phpstan*',
	'phpunit.xml.dist', '.php-cs-fixer.dist.php', 'tsconfig.json', 'postcss.config.mjs', 'blueprint.json',
	'.github/**', '.nvmrc', '.gitignore', '**/*.before-*', '**/*v2*.html',
];
const files = globSync( include, { cwd: ROOT, nodir: true, ignore } ).sort();
const staging = mkdtempSync( path.join( tmpdir(), 'nextsaas-package-' ) );
const packageRoot = path.join( staging, 'nextsaas' );
let bytes = 0;
try {
	for ( const relative of files ) {
		const target = path.join( packageRoot, relative );
		mkdirSync( path.dirname( target ), { recursive: true } );
		cpSync( path.join( ROOT, relative ), target );
		bytes += statSync( target ).size;
	}
	const zipName = `nextsaas-v${ match[ 1 ] }.zip`;
	const zipPath = path.join( ROOT, zipName );
	rmSync( zipPath, { force: true } );
	execFileSync( 'zip', [ '-qr', zipPath, 'nextsaas' ], { cwd: staging, stdio: 'inherit' } );
	console.log( `Created ${ zipName }: ${ files.length } files, ${ bytes } bytes` );
} finally {
	rmSync( staging, { recursive: true, force: true } );
}
