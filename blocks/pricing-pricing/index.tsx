import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { PricingPricingAttributes } from './types';

registerBlockType<PricingPricingAttributes>(metadata as BlockConfiguration<PricingPricingAttributes>, {
	edit: Edit,
	save: () => null,
});
