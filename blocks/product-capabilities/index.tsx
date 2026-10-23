import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ProductCapabilitiesAttributes } from './types';

registerBlockType<ProductCapabilitiesAttributes>(metadata as BlockConfiguration<ProductCapabilitiesAttributes>, {
	edit: Edit,
	save: () => null,
});
