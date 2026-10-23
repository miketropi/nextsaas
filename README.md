# NextSaaS

NextSaaS is a WordPress block child theme for Nextora. It ports the approved cobalt-editorial design across ten surfaces: home, About Us, Contact Us, FAQ, Pricing, archive, post detail, search, 404, and product.

## Requirements

- WordPress 6.4 or newer
- PHP 8.1 or newer
- The Nextora parent theme
- Node.js 24 and npm for asset development
- Composer for PHP quality tooling

## Install and build

Install Nextora and this directory in `wp-content/themes`, then activate NextSaaS.

```sh
npm ci
composer install
npm run build
npm run typecheck
npm run lint:php:all
composer test
npm run test:wp
npm run build:package
```

Compiled assets are committed because WordPress consumes them directly.

## Child-theme architecture

WordPress loads the child `functions.php` **before** the parent's file. Child code therefore must not read a `NEXTORA_*` constant or call a parent function at file scope. Parent integration belongs on hooks that run after both themes have loaded. NextSaaS owns its constants, bootstrap, assets, block registration, templates, parts, and patterns.

`Nextsaas\` is reserved as the Composer PSR-4 prefix for `inc/`. There are no namespaced PHP classes yet; the mapping records the intended namespace without pretending classes already exist.

## Block contract

Each dynamic section lives in `blocks/<slug>/` and supplies `block.json`, typed editor sources, `render.php`, `style.css`, and `editor.css`. Interaction blocks additionally supply `view.ts`. Builds commit `index.js`/`index.asset.php` and, where relevant, `view.js`/`view.asset.php`. Attributes hold editable design content, rendering escapes by context, and frontend behavior has an accessible no-JavaScript state.

## Pattern contract

Every section block has one filesystem pattern in `patterns/<slug>.php`, categorized under `nextsaas-sections`, with a single corresponding block and exact translatable defaults. Hidden page-composition patterns preserve manifest order; templates remain thin compositions.

## Design-token contract

`theme.json` and `resources/css/00-tokens.css` are the shared authority for the mist-white, white, blue-ink, muted blue-gray, hairline, and cobalt palette; Bodoni Moda, DM Sans, and mono font roles; spacing; type scale; and square geometry. Parent-compatible preset slugs are retained so reused Nextora blocks remain styled.

CSS modules live in `resources/css/modules` and compile into one `assets/css/nextsaas.css`. The design cascade depends on module rule order, so modules must not be independently enqueued or reordered casually. Editor styles compile separately to `assets/css/nextsaas-editor.css`.

## Mega-navigation integration

The theme-owned primary menu and compact drawer remain the accessible fallback. When `beplus-visual-mega-nav` is available, NextSaaS adds `primary` through `beplus_vmn_locations` and marks compact navigation with `data-beplus-vmn-compact-mode="accordion"`. The theme owns the single drawer, overlay, focus trap, body lock, Escape handling, and 1024px transition; plugin panels become in-flow compact accordions rather than a second navigation shell.

## Where the CSS lives, and why

Section rules are **not** shipped per block. They live in `resources/css/modules/25-sections.css` and compile into the single `assets/css/nextsaas.css`, because the approved stylesheet is a sequence of unlayered blocks where later rules deliberately override earlier ones — the landing's `/* Preset refresh */` block, the `/* Readability and interaction refinement */` block and the inner-page composition all restate earlier selectors. WordPress prints a `block.json` `style` file late, in the footer, so moving a section's rules into `blocks/<slug>/style.css` would invert that order and silently change typography and spacing. A block therefore ships `style.css` only when it needs a rule the approved render never had, and `block.json` declares the file only then.

Two selector forms are generated for every rule whose subject is a block root: `.nextsaas-scope .hero h1` cannot match, because the hero element *is* the scoped element, so `postcss.config.mjs` also emits `.nextsaas-scope.hero h1`. The same plugin drops the scope from `.inner-page` rules, because that class sits on `<body>` — see `inc/template/body-class.php`.

## Deliberate deviations from the approved render

Each of these is a decision, not an oversight.

| Area | Render | Theme | Why |
|---|---|---|---|
| Navigation breakpoint | drawer at 680px, tablet band 681–920px | one drawer at ≤1023px, desktop bar ≥1024px | `beplus-visual-mega-nav` treats ≥1024px as desktop and everything below as a host-owned compact region; one breakpoint means the plugin can never open a second drawer over the theme's own. |
| Drawer dismissal | none | scrim, focus trap, body lock, Escape, outside click | The render's panel is not a modal, but a full-height drawer needs the modal contract to be usable by keyboard. |
| Header/footer variants | landing and inner variants | one variant using `@home` / `@home#anchor` | On a live site the inner variant's absolute links are the only form that works from every page; on the front page it renders identically. |
| Archive, search, single, index | mock posts | real `core/query` loops | The surfaces are templates, so the posts are the site's. Card art and ordinal counters were design-time decoration and are replaced by the real featured image and terms. |
| Contact form | first/last name, company, enquiry select, consent | the parent `nextora/contact-form` block | The plan requires reusing the parent's submission, nonce, REST, mail and anti-spam path rather than forking it; the parent block owns four fields, and the NextSaaS labels are applied to them. |
| Newsletter | preview-only status | same, plus an optional `action` | The parent has no subscription backend; an editor who wires an endpoint gets a native POST instead of the intercepted preview. |
| Ticker/motion | word-by-word scroll reveal on one landing heading | same, Web Animations API | The render uses no GSAP; the theme vendors no motion library and honours `prefers-reduced-motion`. |
| `nextsaas/*` count | — | 39 blocks for 46 manifest rows | `page-hero` (7 pages) and `closing-cta` (4 pages) are shared blocks whose templates pass the per-page copy. |

## Verification

```sh
npm run typecheck           # tsc, no errors
npm run lint:php:all        # PHPStan level 8 + PHP-CS-Fixer dry run
npm run build               # deterministic: a second run changes nothing
composer test               # 51 unit tests
npm run build:package       # nextsaas-v<version>.zip
```

The unit suite covers block registration and inventory, manifest coverage (every section has a block and a pattern, every template places its sections once in order, every default carries an approved line **and its line structure**), URL and query boundaries, the header walker's ARIA decisions, the asset/licence inventory and the ZIP's contents.

The WordPress proof is separate, because it needs a booted install:

```sh
# Playground (downloads WordPress)
npm run test:wp

# An existing install via WP-CLI
WP_CLI_CMD='php -d mysqli.default_socket=/path/to/mysqld.sock wp --path=/path/to/wp' \
  bash scripts/wp-test.sh --wp-cli
```

`tests/smoke/assertions.php` asserts the child is active over Nextora, every `nextsaas/*` block is registered, the parent contact form is present and not cloned, both pattern categories exist, every pattern parses, every template exists, every block renders non-empty markup, the `primary` location is registered, and nothing raises a notice.

`tests/fixtures/seed.php` creates the ten fixture surfaces, seven posts across three categories and two tags (one without a featured image) and the `primary` menu, so the manifest can be diffed against a live site:

```sh
wp eval 'require_once get_stylesheet_directory() . "/tests/fixtures/seed.php"; nextsaas_seed_fixtures();'
```

Then compare the ordered `data-nextsaas-section` values of each surface with `tests/fixtures/design-manifest.json`.
