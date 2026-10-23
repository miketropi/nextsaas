import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ProductPerspectivesAttributes } from './types';

registerBlockType<ProductPerspectivesAttributes>(metadata as BlockConfiguration<ProductPerspectivesAttributes>, {
	edit: Edit,
	save: () => null,
});
