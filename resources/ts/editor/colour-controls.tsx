/**
 * Section appearance controls shared by every custom NextSaaS block.
 *
 * A NextSaaS section is styled by the theme, so its colours are not attributes the
 * author should have to guess at in CSS: each block stores four optional appearance
 * attributes — `sectionBg`, `sectionHeading`, `sectionText` and `sectionGap` — and
 * its renderer turns them into `--nextsaas-block-*` properties on the block root,
 * with `has-nextsaas-*` presence classes the stylesheet reads. An empty string means
 * "theme default" and the renderer emits nothing for it, so this module keeps that
 * contract by writing `''` when a value is cleared.
 *
 * The module owns three things that must agree with the PHP side exactly:
 *
 * - the colour UI, which stores a picked theme preset as its raw palette slug and
 *   accepts both reference spellings WordPress itself writes
 *   (`var(--wp--preset--color--base)` and `var:preset|color|base`) as input;
 * - the reader that turns a server-rendered section root back into the appearance
 *   facts the inspector needs (which roles the renderer applied, and the contrast
 *   ratios it measured) instead of re-deriving them in JavaScript;
 * - the notice, which is shown only for an author-specified foreground that fails
 *   its minimum ratio or for a changed background the renderer could not measure.
 */

import { useSettings } from '@wordpress/block-editor';
import {
	Button,
	ColorPalette,
	ColorPicker,
	Dropdown,
	Notice,
	PanelBody,
} from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { useEffect, useRef, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

export interface ColourControlsProps {
	attributes: Record<string, unknown>;
	setAttributes: (attributes: Record<string, unknown>) => void;
}

/** One colour attribute and the label shown for it. */
interface ColourRow {
	attribute: string;
	label: string;
}

/** The three colour attributes every supported block declares, in display order. */
const ROWS: ColourRow[] = [
	{ attribute: 'sectionBg', label: __('Background', 'nextsaas') },
	{ attribute: 'sectionHeading', label: __('Heading text', 'nextsaas') },
	{ attribute: 'sectionText', label: __('Body text', 'nextsaas') },
];

/** The four attributes the server-side appearance key is built from, in its order. */
const APPEARANCE_KEYS = [
	'sectionBg',
	'sectionHeading',
	'sectionText',
	'sectionGap',
] as const;

/** One entry of the theme palette as `useSettings('color.palette')` returns it. */
export interface PaletteEntry {
	name?: string;
	slug?: string;
	color?: string;
}

/** A palette entry paired with the preset reference the picker shows and stores. */
interface PaletteSwatch {
	slug: string;
	name: string;
	value: string;
	raw: string;
}

/** sRGB channels in the 0–255 range plus an alpha channel in 0–1. */
interface RgbColor {
	r: number;
	g: number;
	b: number;
	a: number;
}

const HEX_COLOUR = /^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i;
const FUNCTION_COLOUR = /^([a-z]+)\(([^()]*)\)$/;
const SLUG = /^[a-z][a-z0-9-]*$/;

/** Read a stored attribute as a trimmed string. */
function storedAttribute(value: unknown): string {
	return 'string' === typeof value ? value.trim() : '';
}

/** Palette slug referenced by a stored preset reference, or `''`. */
function presetSlugOf(value: string): string {
	const modern = /^var:preset\|color\|([a-z0-9][a-z0-9-]*)$/i.exec(value);

	if (modern) {
		return modern[1].toLowerCase();
	}

	const legacy =
		/^var\(\s*--wp--preset--color--([a-z0-9][a-z0-9-]*)\s*\)$/i.exec(value);

	return legacy ? legacy[1].toLowerCase() : '';
}

/** Parse one colour function argument, scaling a percentage against `scale`. */
function parseNumber(token: string, scale: number): number | null {
	const parsed = /^([-+]?(?:\d+\.?\d*|\.\d+))(deg|%|)$/i.exec(token.trim());

	if (!parsed) {
		return null;
	}

	const number = Number.parseFloat(parsed[1]);

	return '%' === parsed[2].toLowerCase() ? (number / 100) * scale : number;
}

/** Encode linear-light sRGB in 0–1 as sRGB in 0–255. */
function encodeChannel(channel: number): number {
	const clamped = Math.min(1, Math.max(0, channel));
	const encoded =
		clamped <= 0.0031308
			? 12.92 * clamped
			: 1.055 * clamped ** (1 / 2.4) - 0.055;

	return encoded * 255;
}

/** OKLCh to sRGB, so a palette written in `oklch()` can be compared. */
function oklchToRgb(lightness: number, chroma: number, hue: number): RgbColor {
	const radians = (hue * Math.PI) / 180;
	const a = chroma * Math.cos(radians);
	const b = chroma * Math.sin(radians);

	const long = (lightness + 0.3963377774 * a + 0.2158037573 * b) ** 3;
	const medium = (lightness - 0.1055613458 * a - 0.0638541728 * b) ** 3;
	const short = (lightness - 0.0894841775 * a - 1.291485548 * b) ** 3;

	return {
		r: encodeChannel(
			4.0767416621 * long - 3.3077115913 * medium + 0.2309699292 * short,
		),
		g: encodeChannel(
			-1.2684380046 * long + 2.6097574011 * medium - 0.3413193965 * short,
		),
		b: encodeChannel(
			-0.0041960863 * long - 0.7034186147 * medium + 1.707614701 * short,
		),
		a: 1,
	};
}

/** HSL to sRGB. */
function hslToRgb(hue: number, saturation: number, lightness: number): RgbColor {
	const normalizedHue = ((((hue % 360) + 360) % 360) as number) / 60;
	const saturated = Math.min(1, Math.max(0, saturation));
	const light = Math.min(1, Math.max(0, lightness));

	const chroma = (1 - Math.abs(2 * light - 1)) * saturated;
	const second = chroma * (1 - Math.abs((normalizedHue % 2) - 1));
	const offset = light - chroma / 2;

	const sets: Array<[number, number, number]> = [
		[chroma, second, 0],
		[second, chroma, 0],
		[0, chroma, second],
		[0, second, chroma],
		[second, 0, chroma],
		[chroma, 0, second],
	];

	const rgb = sets[Math.floor(normalizedHue) % 6];

	return {
		r: (rgb[0] + offset) * 255,
		g: (rgb[1] + offset) * 255,
		b: (rgb[2] + offset) * 255,
		a: 1,
	};
}

/**
 * Parse a bounded CSS colour into sRGB channels.
 *
 * Only the forms an author can pick or type are understood: hex, `rgb()`,
 * `rgba()`, `hsl()`, `hsla()`, `oklch()` and `transparent`. That is exactly the
 * set the PHP renderer accepts, so the editor never normalises a value the server
 * would refuse to emit.
 */
function parseCssColor(value: string): RgbColor | null {
	const input = value.trim().toLowerCase();

	if ('' === input) {
		return null;
	}

	if ('transparent' === input) {
		return { r: 0, g: 0, b: 0, a: 0 };
	}

	if (input.startsWith('#')) {
		let hex = input.slice(1);

		if (!HEX_COLOUR.test(input)) {
			return null;
		}

		if (hex.length < 6) {
			hex = hex
				.split('')
				.map((character) => character + character)
				.join('');
		}

		return {
			r: Number.parseInt(hex.slice(0, 2), 16),
			g: Number.parseInt(hex.slice(2, 4), 16),
			b: Number.parseInt(hex.slice(4, 6), 16),
			a: 8 === hex.length ? Number.parseInt(hex.slice(6, 8), 16) / 255 : 1,
		};
	}

	const parsed = FUNCTION_COLOUR.exec(input);

	if (!parsed) {
		return null;
	}

	const list = parsed[2].trim().split(/[\s,/]+/);

	if (list.length === 0) {
		return null;
	}

	const alpha = (token: string | undefined): number | null =>
		undefined === token ? 1 : parseNumber(token, 1);

	if ('rgb' === parsed[1] || 'rgba' === parsed[1]) {
		if (list.length < 3 || list.length > 4) {
			return null;
		}

		const channels = list
			.slice(0, 3)
			.map((token) => parseNumber(token, 255));

		const transparency = alpha(list[3]);

		if (channels.some((channel) => null === channel) || null === transparency) {
			return null;
		}

		return {
			r: Math.min(255, Math.max(0, channels[0] as number)),
			g: Math.min(255, Math.max(0, channels[1] as number)),
			b: Math.min(255, Math.max(0, channels[2] as number)),
			a: Math.min(1, Math.max(0, transparency)),
		};
	}

	if ('hsl' === parsed[1] || 'hsla' === parsed[1]) {
		if (list.length < 3 || list.length > 4) {
			return null;
		}

		const hue = parseNumber(list[0], 360);
		const saturation = parseNumber(list[1], 1);
		const lightness = parseNumber(list[2], 1);
		const transparency = alpha(list[3]);

		if (
			null === hue ||
			null === saturation ||
			null === lightness ||
			null === transparency
		) {
			return null;
		}

		return {
			...hslToRgb(hue, saturation, lightness),
			a: Math.min(1, Math.max(0, transparency)),
		};
	}

	if ('oklch' === parsed[1]) {
		if (list.length < 3 || list.length > 4) {
			return null;
		}

		const lightness = parseNumber(list[0], 1);
		const chroma = parseNumber(list[1], 1);
		const hue = parseNumber(list[2], 360);
		const transparency = alpha(list[3]);

		if (
			null === lightness ||
			null === chroma ||
			null === hue ||
			null === transparency
		) {
			return null;
		}

		return {
			...oklchToRgb(
				Math.min(1, Math.max(0, lightness > 1 ? lightness / 100 : lightness)),
				Math.max(0, chroma),
				hue,
			),
			a: Math.min(1, Math.max(0, transparency)),
		};
	}

	return null;
}

/** Encode an opaque colour as a six-digit hex, which is all `ColorPicker` round-trips. */
function toHex(color: RgbColor): string {
	const channel = (value: number): string =>
		Math.round(Math.min(255, Math.max(0, value)))
			.toString(16)
			.padStart(2, '0');

	return `#${channel(color.r)}${channel(color.g)}${channel(color.b)}`;
}

/** Whether two opaque colours are the same colour up to rounding. */
function sameColour(first: RgbColor, second: RgbColor): boolean {
	return (
		Math.abs(first.r - second.r) <= 2 &&
		Math.abs(first.g - second.g) <= 2 &&
		Math.abs(first.b - second.b) <= 2
	);
}

/** The active palette, each entry carrying the reference the swatch shows. */
function paletteSwatches(palette: readonly PaletteEntry[]): PaletteSwatch[] {
	return palette
		.filter(
			(entry): entry is PaletteEntry & { color: string } =>
				!!entry && 'string' === typeof entry.color,
		)
		.map((entry) => ({
			slug: (entry.slug ?? '').toLowerCase(),
			name: entry.name ?? entry.color,
			value: entry.slug
				? `var(--wp--preset--color--${entry.slug})`
				: entry.color,
			raw: entry.color,
		}))
		.filter((swatch) => '' !== swatch.slug);
}

/**
 * The value a colour attribute should be stored as.
 *
 * A picked theme preset is stored as its raw palette slug, which is the only form
 * that keeps following the theme when the preset's value changes. A literal is
 * stored as typed, except that a literal which *is* an opaque palette colour is
 * stored as that palette's slug instead — a partial-alpha colour is never promoted
 * to a preset, because the preset is opaque and the alpha would be lost.
 */
export function normalizeColorForStorage(
	value: string,
	palette: readonly PaletteEntry[],
): string {
	const input = value.trim();

	if ('' === input) {
		return '';
	}

	const swatches = paletteSwatches(palette);
	const referenced = presetSlugOf(input);

	if ('' !== referenced) {
		return swatches.some((swatch) => swatch.slug === referenced)
			? referenced
			: input;
	}

	if (SLUG.test(input) && swatches.some((swatch) => swatch.slug === input)) {
		return input;
	}

	const parsed = parseCssColor(input);

	if (!parsed || parsed.a < 1) {
		return input;
	}

	const match = swatches.find((swatch) => {
		const candidate = parseCssColor(swatch.raw);

		return !!candidate && candidate.a >= 1 && sameColour(candidate, parsed);
	});

	return match ? match.slug : input;
}

/**
 * The hex a stored colour shows in the custom picker.
 *
 * A preset-backed colour is shown in the palette instead, so the picker starts
 * empty for it; a literal is converted to hex so an `oklch()`, `rgb()` or `hsl()`
 * value the author stored still round-trips through the picker.
 */
export function colorValueForPicker(
	value: string,
	palette: readonly PaletteEntry[],
): string | undefined {
	const input = value.trim();

	if ('' === input) {
		return undefined;
	}

	if (HEX_COLOUR.test(input)) {
		return input;
	}

	const swatches = paletteSwatches(palette);

	if (
		'' !== presetSlugOf(input) ||
		swatches.some((swatch) => swatch.slug === input)
	) {
		return undefined;
	}

	const parsed = parseCssColor(input);

	return parsed ? toHex(parsed) : undefined;
}

/**
 * The reference a stored colour shows as in the palette.
 *
 * @param value   Stored attribute value.
 * @param palette Active palette.
 *
 * @return Preset reference, or undefined when the colour is not a preset.
 */
function paletteValueOf(
	value: string,
	palette: readonly PaletteEntry[],
): string | undefined {
	const input = value.trim();
	const swatches = paletteSwatches(palette);
	const referenced = presetSlugOf(input);
	const slug = '' !== referenced ? referenced : input;

	if (
		!SLUG.test(slug) ||
		!swatches.some((swatch) => swatch.slug === slug)
	) {
		return undefined;
	}

	return `var(--wp--preset--color--${slug})`;
}

/**
 * The appearance facts a rendered section root reports.
 *
 * Every field is read from the markup the PHP renderer produced: the presence
 * classes say which roles are in force, the custom properties are the values the
 * renderer resolved (including the ones it derived), the ratios are the ones it
 * measured, and the key identifies the exact attribute values the response was
 * built from, so a response that arrives after the author changed a control can be
 * discarded instead of shown.
 */
export interface SectionAppearanceReading {
	key: string;
	classes: string[];
	properties: Record<string, string>;
	hasBackground: boolean;
	hasHeading: boolean;
	hasContent: boolean;
	derivedHeading: boolean;
	derivedContent: boolean;
	headingRatio: number | null;
	contentRatio: number | null;
}

/** The key a response for these attributes must carry to be current. */
export function appearanceKeyOf(attributes: Record<string, unknown>): string {
	return APPEARANCE_KEYS.map((key) => storedAttribute(attributes[key])).join('|');
}

/** Read a numeric ratio data attribute, or null when it is `unknown`. */
function ratioAttribute(root: Element, name: string): number | null {
	const raw = root.getAttribute(name);

	if (!raw || 'unknown' === raw) {
		return null;
	}

	const parsed = Number.parseFloat(raw);

	return Number.isFinite(parsed) ? parsed : null;
}

/**
 * Read the appearance a server-rendered section root reports.
 *
 * @param root Element to read, typically the rendered block root.
 *
 * @return Reading, or null when the element carries no appearance at all.
 */
export function readSectionAppearance(
	root: Element | null | undefined,
): SectionAppearanceReading | null {
	const element = root && 1 === root.nodeType ? root : null;
	const key = element ? element.getAttribute('data-nextsaas-appearance-key') : null;

	if (!element || null === key) {
		return null;
	}

	const style = (element as HTMLElement).style;
	const properties: Record<string, string> = {};

	for (let index = 0; index < style.length; index += 1) {
		const name = style.item(index);

		if (name.startsWith('--nextsaas-')) {
			properties[name] = style.getPropertyValue(name).trim();
		}
	}

	return {
		key,
		classes: Array.from(element.classList).filter((name) =>
			name.startsWith('has-nextsaas-'),
		),
		properties,
		hasBackground: element.classList.contains('has-nextsaas-background'),
		hasHeading: element.classList.contains('has-nextsaas-heading'),
		hasContent: element.classList.contains('has-nextsaas-content'),
		derivedHeading: element.classList.contains('has-nextsaas-derived-heading'),
		derivedContent: element.classList.contains('has-nextsaas-derived-content'),
		headingRatio: ratioAttribute(element, 'data-nextsaas-heading-ratio'),
		contentRatio: ratioAttribute(element, 'data-nextsaas-content-ratio'),
	};
}

export interface AppearanceNoticeProps {
	/** Current block attributes, read for the roles the author set. */
	attributes: Record<string, unknown>;
	/** The reading for those exact attributes, or null while there is none. */
	appearance: SectionAppearanceReading | null;
	/** Clears the two foreground attributes on an explicit author action. */
	onUseThemeForegrounds: () => void;
}

/**
 * The contrast feedback for one section.
 *
 * Shown only where it is actionable: an author-specified foreground that fails its
 * minimum ratio, or a changed background the renderer could not measure. A section
 * whose derived foregrounds succeeded needs no notice — the preview already shows
 * the result, and the ratios stay readable on the root for anyone inspecting it.
 */
export function AppearanceNotice({
	attributes,
	appearance,
	onUseThemeForegrounds,
}: AppearanceNoticeProps): JSX.Element | null {
	if (!appearance) {
		return null;
	}

	const background = storedAttribute(attributes.sectionBg);
	const heading = storedAttribute(attributes.sectionHeading);
	const text = storedAttribute(attributes.sectionText);
	const problems: string[] = [];

	const report = (
		value: string,
		ratio: number | null,
		minimum: number,
		label: string,
	): void => {
		if ('' === value) {
			return;
		}

		if (null === ratio) {
			problems.push(
				sprintf(
					/* translators: %s: colour role, e.g. "Heading text". */
					__(
						'%s contrast could not be measured against the section background. The stored colour was kept and the theme fallback is used.',
						'nextsaas',
					),
					label,
				),
			);

			return;
		}

		if (ratio < minimum) {
			problems.push(
				sprintf(
					/* translators: 1: colour role, 2: measured ratio, 3: minimum ratio. */
					__(
						'%1$s contrast is %2$s:1, below the %3$s:1 minimum. The stored colour was kept.',
						'nextsaas',
					),
					label,
					ratio.toFixed(2),
					minimum.toFixed(1),
				),
			);
		}
	};

	report(heading, appearance.headingRatio, 3, __('Heading text', 'nextsaas'));
	report(text, appearance.contentRatio, 4.5, __('Body text', 'nextsaas'));

	if ('' !== background) {
		if ('' === heading && null === appearance.headingRatio) {
			problems.push(
				__(
					'Heading text could not be measured against this background, so the theme heading colour is kept.',
					'nextsaas',
				),
			);
		}

		if ('' === text && null === appearance.contentRatio) {
			problems.push(
				__(
					'Body text could not be measured against this background, so the theme content colour is kept.',
					'nextsaas',
				),
			);
		}
	}

	if (0 === problems.length) {
		return null;
	}

	const clearable = '' !== heading || '' !== text;

	return (
		<Notice
			className="nextsaas-block-preview__notice"
			status="warning"
			isDismissible={false}
			actions={
				clearable
					? [
							{
								label: __('Use theme foregrounds', 'nextsaas'),
								onClick: onUseThemeForegrounds,
								variant: 'secondary' as const,
							},
						]
					: undefined
			}
		>
			{problems.map((problem) => (
				<p key={problem}>{problem}</p>
			))}
		</Notice>
	);
}

export interface SectionAppearancePreviewProps {
	/** Registered block name the appearance is asked of. */
	block: string;
	/** Current block attributes, forwarded verbatim to the PHP renderer. */
	attributes: Record<string, unknown>;
	/** Called with the reading for the current attributes, or null when there is none. */
	onAppearance: (appearance: SectionAppearanceReading | null) => void;
}

/**
 * Placeholder rendered inside the hidden container when the preview request fails.
 *
 * It reports through the effect rather than during render, so a failed request
 * becomes visible as an editor notice without ever touching the author's
 * attributes.
 */
function AppearancePreviewError({
	message,
	onError,
}: {
	message?: string;
	onError: (message: string) => void;
}): JSX.Element {
	useEffect(() => {
		onError(
			message ||
				__(
					'The appearance preview could not be loaded. Your colours are unchanged.',
					'nextsaas',
				),
		);
	}, [message, onError]);

	return <span />;
}

/**
 * A hidden server render used only for the appearance of a natively edited block.
 *
 * The contact section keeps a native `useBlockProps()` wrapper and a live
 * `InnerBlocks` form, so it has no server-rendered root to read. Rather than grow a
 * second derivation algorithm in JavaScript, this renders the same block through
 * the same REST route in a hidden, `aria-hidden` container and reports what the
 * renderer says. Nothing from the response is applied to the author's attributes;
 * the caller applies the classes and custom properties to its own wrapper.
 */
export function SectionAppearancePreview({
	block,
	attributes,
	onAppearance,
}: SectionAppearancePreviewProps): JSX.Element {
	const containerRef = useRef<HTMLDivElement>(null);
	const callbackRef = useRef(onAppearance);
	const [error, setError] = useState('');

	callbackRef.current = onAppearance;

	const report = (message: string): void => {
		setError(message);
		callbackRef.current(null);
	};

	// A new request supersedes the previous failure; the notice is not sticky.
	useEffect(() => {
		setError('');
	}, [attributes]);

	useEffect(() => {
		const container = containerRef.current;

		if (!container) {
			return;
		}

		const read = (): void => {
			const root = container.querySelector('[data-nextsaas-section]');

			callbackRef.current(readSectionAppearance(root));
		};

		read();

		const observer = new MutationObserver(read);

		observer.observe(container, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [
				'class',
				'style',
				'data-nextsaas-appearance-key',
				'data-nextsaas-heading-ratio',
				'data-nextsaas-content-ratio',
			],
		});

		return () => observer.disconnect();
	}, [block]);

	return (
		<>
			<div
				className="nextsaas-appearance-preview"
				ref={containerRef}
				hidden
				aria-hidden="true"
			>
				<ServerSideRender
					block={block}
					attributes={attributes}
					LoadingResponsePlaceholder={() => <span />}
					EmptyResponsePlaceholder={() => <span />}
					ErrorResponsePlaceholder={({
						message,
					}: {
						message?: string;
					}) => <AppearancePreviewError message={message} onError={report} />}
				/>
			</div>
			{'' !== error ? (
				<Notice status="warning" isDismissible={false}>
					{error}
				</Notice>
			) : null}
		</>
	);
}

export function ColourControls({
	attributes,
	setAttributes,
}: ColourControlsProps): JSX.Element {
	// `useSettings` resolves every requested path and returns them in order;
	// the older single-path `useSetting` is deprecated since WordPress 6.5 and
	// is no longer in the package's types.
	const [setting] = useSettings('color.palette') as [
		PaletteEntry[] | undefined,
	];
	const instanceId = useInstanceId(ColourControls, 'nextsaas-colour');
	const palette = setting ?? [];
	const swatches = paletteSwatches(palette);

	return (
		<PanelBody title={__('Colours', 'nextsaas')} initialOpen={false}>
			<div style={{ display: 'grid', gap: '16px' }}>
				{ROWS.map((row) => {
					const value = storedAttribute(attributes[row.attribute]);
					const labelId = `${instanceId}-${row.attribute}-label`;
					const inputId = `${instanceId}-${row.attribute}`;
					const set = (next: string): void =>
						setAttributes({
							[row.attribute]: normalizeColorForStorage(next, palette),
						});

					return (
						<div className="components-base-control" key={row.attribute}>
							<div className="components-base-control__field">
								<span className="components-base-control__label" id={labelId}>
									{row.label}
								</span>
								<ColorPalette
									id={inputId}
									aria-labelledby={labelId}
									colors={swatches.map((swatch) => ({
										name: swatch.name,
										color: swatch.value,
									}))}
									value={paletteValueOf(value, palette)}
									clearable
									disableCustomColors
									__experimentalIsRenderedInSidebar
									onChange={(next) => set(next ?? '')}
								/>
								<div
									style={{
										display: 'flex',
										gap: '8px',
										alignItems: 'center',
										marginTop: '8px',
									}}
								>
									<Dropdown
										popoverProps={{ placement: 'bottom-start' }}
										renderToggle={({ isOpen, onToggle }) => (
											<Button
												aria-expanded={isOpen}
												aria-label={`${__('Custom colour', 'nextsaas')}: ${row.label}`}
												onClick={onToggle}
												size="small"
												variant="secondary"
											>
												{__('Custom…', 'nextsaas')}
											</Button>
										)}
										renderContent={() => (
											<div style={{ padding: 16 }}>
												<ColorPicker
													color={colorValueForPicker(value, palette)}
													enableAlpha={false}
													onChange={(next) => set(next)}
												/>
											</div>
										)}
									/>
									<Button
										aria-label={`${__('Clear', 'nextsaas')}: ${row.label}`}
										disabled={'' === value}
										onClick={() => set('')}
										size="small"
										variant="tertiary"
									>
										{__('Clear', 'nextsaas')}
									</Button>
								</div>
							</div>
						</div>
					);
				})}
			</div>
		</PanelBody>
	);
}

export default ColourControls;
