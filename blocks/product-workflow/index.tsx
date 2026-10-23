import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ProductWorkflowAttributes } from './types';

registerBlockType<ProductWorkflowAttributes>(metadata as BlockConfiguration<ProductWorkflowAttributes>, {
	edit: Edit,
	save: () => null,
});
