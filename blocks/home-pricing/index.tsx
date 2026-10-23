import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomePricingAttributes } from './types';

registerBlockType<HomePricingAttributes>(metadata as BlockConfiguration<HomePricingAttributes>, {
	edit: Edit,
	save: () => null,
});
