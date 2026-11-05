# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Added

- Every custom block declares the same four optional appearance attributes — `sectionBg`, `sectionHeading`, `sectionText` and `sectionGap` — with a `Colours` panel and a `Spacing` panel, native Dimensions spacing, and core's own colour controls disabled so there is one colour UI rather than two owners.
- `nextsaas_section_appearance()` turns those attributes into `has-nextsaas-*` presence classes and `--nextsaas-block-*` properties on the block root; `nextsaas_resolve_section_color()` and `nextsaas_resolve_section_gap()` accept only palette references, spacing presets and bounded colour literals, so a hostile stored value emits nothing.
- `nextsaas_section_foregrounds()` derives a readable heading/content/focus pair when the author changed the background and left a foreground empty, measured with WCAG ratios against the real page canvas; the roles are emitted as `--nextsaas-derived-*` properties and the ratios as data attributes, so the editor notice can never disagree with the page.
- `resources/ts/editor/spacing-controls.tsx`, plus `AppearanceNotice`, `readSectionAppearance()` and `SectionAppearancePreview` in the shared colour module. The last one gives the natively edited contact section the same appearance values by reading a hidden server render instead of a second derivation algorithm.
- Replaceable hero decoration: `decorationMediaId`, `decorationMediaUrl`, `decorationMediaAlt` and `decorationRotate`, with a `Hero decoration` panel. The approved star SVG returns when no image is selected, and rotation is a switch.
- `nextsaas-content-image`, `nextsaas-portrait-image`, `nextsaas-icon-image`, `hero-decoration-image`, `capability-image` and `team-panel-image`: real images keep their intrinsic proportions instead of the reservation box ratios.
- Independent capability screenshots: two pickers that patch one row of the shared `media` attribute each, merged over the manifest's own default rows so a short saved array still renders both labelled reservations.
- Per-row images: `Panel image` for every team-solutions panel (falling back to the shared default panel image, then to the placeholder), and `Icon image` for every automation step and impact feature (falling back to the stored icon SVG).
- Testimonial attribution is editable: the Stories repeater carries the `Customer name, role & company` field that was already stored and rendered.
- Home pricing: a `Link URL` per plan rendered through `nextsaas_cta()`, and an unrestricted `Features` textarea.

### Changed

- `BlockPreview` splits native spacing between the editor wrapper (margin) and the rendered section (padding), so a section's top spacing applies exactly once in the editor and once on the front end.
- `nextsaas_media()` declares each attachment image's own aspect ratio. WordPress marks library images with `sizes="auto, …"`, and a browser that implements auto sizes lays such an image out as size-contained — which discarded the ratio behind `height: auto` and rendered a 1168×584 photograph at 1168×1500.
- `nextsaas_cta()` accepts an optional `richPath` for a per-row call to action, and reads its own row through `nextsaas_attr()`; callers without it still emit the literal `ctaLabel` marker.
- Home pricing plan rows read every field through `nextsaas_attr()`, so a row saved before a field existed renders without a PHP notice.
- Home pricing renders an unrestricted feature list, skipping blank lines, and the plan CTA is a real link once a destination is set.
- The carousel's per-card width formula uses the selected section gap while keeping each breakpoint's column count.

### Fixed

- Duplicate `Section` panel in the Home testimonial editor.
- Home pricing exposed `Featured` twice; only the item's own `Feature this plan` toggle remains.

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
