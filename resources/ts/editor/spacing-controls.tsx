/**
 * Internal gap control shared by every custom NextSaaS block.
 *
 * A section's top spacing is native: the block declares `supports.spacing`, so the
 * Dimensions panel already offers padding-top and margin-top from theme.json. What
 * native spacing cannot express is the space *between* the items of the section's
 * own layout — `.hero-main`, `.price-grid`, `.faq` and the rest are the section's
 * containers, not a core layout block — so this control owns exactly that one gap.
 *
 * The value is always a spacing preset reference, never a copied length: changing
 * the size behind the preset then changes every section that selected it, and the
 * theme's own preset scale stays the single source of spacing values. `None` is a
 * real override of `0`, while `Theme default` stores an empty string so the
 * renderer emits nothing and the design's own gap survives untouched.
 */

import { useSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export interface SpacingControlsProps {
	attributes: Record<string, unknown>;
	setAttributes: (attributes: Record<string, unknown>) => void;
}

/** One entry of the theme's spacing scale as `useSettings()` returns it. */
interface SpacingSize {
	name?: string;
	slug?: string;
	size?: string;
}

/** Stored value meaning "emit nothing and keep the design's own gap". */
const THEME_DEFAULT = '';

/** Stored value meaning "no gap at all", which is a real override. */
const NO_GAP = '0';

export function SpacingControls({
	attributes,
	setAttributes,
}: SpacingControlsProps): JSX.Element {
	const [setting] = useSettings('spacing.spacingSizes') as [
		SpacingSize[] | undefined,
	];
	const value = 'string' === typeof attributes.sectionGap ? attributes.sectionGap : '';

	const options = [
		{ label: __('Theme default', 'nextsaas'), value: THEME_DEFAULT },
		{ label: __('None', 'nextsaas'), value: NO_GAP },
		...(setting ?? [])
			.filter(
				(size): size is SpacingSize & { slug: string } =>
					!!size && 'string' === typeof size.slug && '' !== size.slug,
			)
			.map((size) => ({
				label: size.name ?? size.slug,
				value: `var:preset|spacing|${size.slug}`,
			})),
	];

	return (
		<PanelBody title={__('Spacing', 'nextsaas')} initialOpen={false}>
			<SelectControl
				__nextHasNoMarginBottom
				label={__('Gap between items', 'nextsaas')}
				help={__(
					'Space between the items of this section’s main layout. The section’s own top spacing lives in the Dimensions panel.',
					'nextsaas',
				)}
				value={value}
				options={options}
				onChange={(sectionGap) => setAttributes({ sectionGap })}
			/>
		</PanelBody>
	);
}

export default SpacingControls;
