import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PricingCompareAttributes } from './types';

registerBlockType<PricingCompareAttributes>(metadata as BlockConfiguration<PricingCompareAttributes>, {
	edit: Edit,
	save: () => null,
});
