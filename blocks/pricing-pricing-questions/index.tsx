import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PricingQuestionsAttributes } from './types';

registerBlockType<PricingQuestionsAttributes>(metadata as BlockConfiguration<PricingQuestionsAttributes>, {
	edit: Edit,
	save: () => null,
});
