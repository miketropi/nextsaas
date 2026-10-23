import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { HomeWorkflowAttributes } from './types';

registerBlockType<HomeWorkflowAttributes>(metadata as BlockConfiguration<HomeWorkflowAttributes>, {
	edit: Edit,
	save: () => null,
});
