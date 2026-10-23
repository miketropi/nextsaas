/**
 * Editor for `nextsaas/post-related-reading`.
 *
 * The list is a real query, so the sidebar carries the query controls — how many
 * stories, and optionally which terms to require — plus the two strings the section
 * prints. Terms are chosen from the site's real taxonomy through the entity store, so
 * an editor picks names and the block stores ids.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { BaseControl, FormTokenField, PanelBody, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { PostRelatedReadingAttributes } from './types';

/** The two values the renderer marks in the section header. */
const targets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
];

/** One selectable term, as the token field sees it. */
interface TermChoice {
	id: number;
	name: string;
}

/**
 * The slice of the core-data store this block reads.
 *
 * `select()` is declared as returning `unknown` for a store whose selectors are not
 * registered with the compiler, and the entity store's own types live beside the
 * package rather than the store name. The slice is one method wide, so the shape is
 * named here and whatever comes back is still checked by `isTermChoice()` before use.
 */
interface CoreDataSelectors {
	getEntityRecords: (
		kind: string,
		name: string,
		query?: Record<string, unknown>,
	) => unknown;
}

/**
 * Whether an entity record carries the two fields the field needs.
 *
 * The entity store returns loosely typed records, so the shape is checked once here
 * rather than trusted at each use.
 *
 * @param record Candidate record.
 * @return True when it is a term with an id and a name.
 */
function isTermChoice(record: unknown): record is { id: number; name: string } {
	return (
		!!record &&
		'object' === typeof record &&
		'id' in record &&
		'number' === typeof (record as { id: unknown }).id &&
		'name' in record &&
		'string' === typeof (record as { name: unknown }).name
	);
}

/**
 * Terms of a taxonomy.
 *
 * @param taxonomy Taxonomy slug.
 * @return Choices.
 */
function useTerms(taxonomy: string): TermChoice[] {
	return useSelect(
		(select) => {
			const entityStore = select(coreStore) as unknown as CoreDataSelectors;

			const records = entityStore.getEntityRecords('taxonomy', taxonomy, {
				per_page: 100,
				_fields: 'id,name',
				orderby: 'name',
				order: 'asc',
			});

			if (!Array.isArray(records)) {
				return [];
			}

			return records
				.filter(isTermChoice)
				.map((record) => ({ id: record.id, name: record.name }));
		},
		[taxonomy],
	);
}

/**
 * Turn stored ids into the tokens the field shows.
 *
 * @param value Stored attribute value.
 * @param terms Available terms.
 * @return Token labels.
 */
function toTokens(value: unknown, terms: TermChoice[]): string[] {
	if (!Array.isArray(value)) {
		return [];
	}

	return value
		.map((id) => terms.find((term) => term.id === id)?.name)
		.filter((name): name is string => 'string' === typeof name);
}

/**
 * Turn chosen tokens back into ids.
 *
 * The token field can hand back either a plain label or an object carrying
 * `value`, depending on how the token arrived, so both shapes are accepted.
 *
 * @param tokens Token values.
 * @param terms  Available terms.
 * @return Stored ids.
 */
function toIds(tokens: Array<string | { value: string }>, terms: TermChoice[]): number[] {
	return tokens
		.map((token) => ('string' === typeof token ? token : token.value))
		.map((name) => terms.find((term) => term.name === name)?.id)
		.filter((id): id is number => 'number' === typeof id);
}

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<PostRelatedReadingAttributes>): JSX.Element {
	const categories = useTerms('category');
	const tags = useTerms('post_tag');

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Query', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Number of stories', 'nextsaas')}
						type="number"
						min={1}
						max={24}
						value={String(attributes.postsPerPage ?? 3)}
						onChange={(value) =>
							setAttributes({ postsPerPage: Number.parseInt(value, 10) || 3 })
						}
					/>
					<BaseControl
						id="nextsaas-related-categories"
						__nextHasNoMarginBottom
						help={__(
							'Left empty, the block uses the post’s own categories. A chosen list narrows the query instead.',
							'nextsaas'
						)}
					>
						<FormTokenField
							label={__('Categories', 'nextsaas')}
							value={toTokens(attributes.categories, categories)}
							suggestions={categories.map((term) => term.name)}
							onChange={(tokens) =>
								setAttributes({ categories: toIds(tokens, categories) })
							}
						/>
					</BaseControl>
					<BaseControl
						id="nextsaas-related-tags"
						__nextHasNoMarginBottom
						help={__(
							'Left empty, the block uses the post’s own tags.',
							'nextsaas'
						)}
					>
						<FormTokenField
							label={__('Tags', 'nextsaas')}
							value={toTokens(attributes.tags, tags)}
							suggestions={tags.map((term) => term.name)}
							onChange={(tokens) => setAttributes({ tags: toIds(tokens, tags) })}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody title={__('Wording', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Read-more label', 'nextsaas')}
						value={attributes.readLabel ?? ''}
						onChange={(readLabel) => setAttributes({ readLabel })}
					/>
					<TextControl
						label={__('Card kicker', 'nextsaas')}
						help={__('The small label above each card’s category.', 'nextsaas')}
						value={attributes.artLabel ?? ''}
						onChange={(artLabel) => setAttributes({ artLabel })}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/post-related-reading" attributes={attributes} />

			<RichTextOverlay
				targets={targets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<PostRelatedReadingAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
