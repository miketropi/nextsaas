import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ProductControlAttributes } from './types';

registerBlockType<ProductControlAttributes>(metadata as BlockConfiguration<ProductControlAttributes>, {
	edit: Edit,
	save: () => null,
});
