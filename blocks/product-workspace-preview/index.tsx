import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';
import type { ProductWorkspacePreviewAttributes } from './types';

registerBlockType<ProductWorkspacePreviewAttributes>(metadata as BlockConfiguration<ProductWorkspacePreviewAttributes>, {
	edit: Edit,
	save: () => null,
});
