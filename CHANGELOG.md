# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

## [1.0.0]

### Added

- NextSaaS child theme of Nextora: theme header, `NEXTSAAS_VERSION`, bootstrap that loads only child modules, and no parent symbol read at file scope.
- Thirty-nine `nextsaas/*` dynamic blocks covering all 46 manifest rows of the approved OpenDesign render across Home, About Us, Contact Us, FAQ, Pricing, the journal archive, post detail, search, the not-found page and Product.
- `nextsaas/page-hero` and `nextsaas/closing-cta` as shared blocks for the two sections the render repeats across pages.
- One filesystem pattern per section block under `nextsaas-sections`, plus a hidden page-composition pattern per surface under `nextsaas-pages`.
- Twelve templates: the ten surfaces, a generic page fallback and the posts-index composition.
- Reuse of the parent `nextora/contact-form` inside the contact section through a locked InnerBlocks region — no forked submission, nonce, REST route, mail handler or anti-spam code.
- Theme-owned primary navigation with a `wp_nav_menu()` walker, a designed fallback while no menu is assigned, and one compact drawer at ≤1023px with scrim, focus trap, body lock, Escape and resize cleanup.
- Optional `beplus-visual-mega-nav` integration: `primary` added through `beplus_vmn_locations`, and `data-beplus-vmn-compact-mode="accordion"` so plugin panels render in flow inside the theme's drawer.
- `theme.json` retaining every Nextora preset, custom template and template part, with the cobalt palette, Bodoni Moda and DM Sans, and the fluid type scale.
- `resources/css` modules compiled into one `assets/css/nextsaas.css`, plus `assets/css/nextsaas-editor.css` for the inspector layer, with a PostCSS transform that also matches root-subject and `inner-page` selectors.
- `tests/fixtures/design-manifest.json`: the checked-in behaviour-defining record of the approved render.
- Unit suite (block registration, manifest coverage and line structure, URL/query boundaries, header walker ARIA, assets and licences), a WordPress smoke script and a fixture seeder.
- Release packaging: `nextsaas-v<version>.zip` containing only runtime files.

### Changed

- Section CSS is centralised rather than shipped per block, because the approved cascade depends on rule order and a block `style` file would print after the compiled sheet.

### Fixed

- The footer notice dialog resolves its heading through the dialog's own `aria-labelledby` instead of a duplicated selector.
- FAQ category filters treat the first filter as "no restriction" by value, so the list is never hidden by the sentinel category.
- Bundled `assets/` media paths resolve through the theme URI instead of the site root.
- Placeholder reservation boxes take their width from their grid track, which removed a 78px horizontal overflow in the product capability panels at 1024–1023px.
